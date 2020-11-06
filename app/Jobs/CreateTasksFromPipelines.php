<?php

namespace App\Jobs;

use App\Services\Pipeline\PipelineService;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use function MongoDB\BSON\toJSON;
use Webklex\IMAP\Client;
use Webklex\IMAP\Exceptions\ConnectionFailedException;
use Webklex\IMAP\Exceptions\GetMessagesFailedException;
use Webklex\IMAP\Exceptions\MessageSearchValidationException;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class CreateTasksFromPipelines
 *
 * @package App\Jobs
 *
 * @author  LexXurio
 */
class CreateTasksFromPipelines implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const OC_FOLDER = 'OC Tasks';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @param PipelineService $service
     *
     * @return void
     * @todo Error handling
     * @todo notify the user
     */
    public function handle(PipelineService $service)
    {
        $pipelines = app('PipelineRepo')->all()->where('is_active', 1);

        $pingDomain = function(string $domain)
        {
            $startTime = microtime(true);
            $file      = fsockopen ($domain, 80, $errno, $errstr, 2);
            $stopTime  = microtime(true);

            if (!$file) {
                $status = -1; // Site is down
            } else {
                fclose($file);
                $status = ($stopTime - $startTime) * 1000;
                $status = floor($status);
            }
            return $status;
        };

        /** @var Pipeline $pipeline */
        foreach ($pipelines as $pipeline) {

            /** @var Client $client */
            $client = $service->getMailClientForPipeline($pipeline->id);
            try {
                if($pingDomain($client->host) < 0) {
                    throw new \Exception();
                }
            } catch (\Exception $e) {
                $pipeline->is_active = 0;
                $pipeline->save();

                Log::warning(__CLASS__ . '::' . __METHOD__ .' pipeline host ' . $client->host . ' is unreachable');
                continue;
            }


            try {
                $client->connect();

                /** @var \Webklex\IMAP\Support\FolderCollection $folders */
                $folders = $client->getFolders(false);
            } catch (ConnectionFailedException $e) {
                continue;
            }

            $ocFolderExist = false;
            /** @var \Webklex\IMAP\Folder $folder */
            foreach ($folders as $folder) {
                if ($folder->name === self::OC_FOLDER) {
                    $ocFolderExist = true;
                    break;
                }
            }

            if (!$ocFolderExist) {
                $ocFolder = $client->createFolder('{imap.'.$pipeline->host.':'.$pipeline->port.'}INBOX.'.self::OC_FOLDER);
            }

            /** @var \Webklex\IMAP\Folder $inboxFolder */
            $inboxFolder = $client->getFolder('INBOX');

            /** @var Collection $distRules */
            $distRules = $pipeline->rules;
            foreach ($distRules as $distRule) {

                $filterBy = '';
                if (strpos($distRule->filter->name, '_')) {
                    //
                } else {
                    $filterBy = $distRule->filter->name;
                }
                $filterBy = strtoupper($filterBy);

                $keywords = [];
                foreach ($distRule->keywords as $keyword) {
                    $keywords[] = [$filterBy, $keyword];
                }

                $boardIds = [];
                /** @var \App\Models\Board $board */
                foreach ($distRule->boards as $board) {
                    $boardIds[] = $board->id;
                }
                try {
                    /** @var \Webklex\IMAP\Support\MessageCollection $mails */
                    $mails = $inboxFolder->searchMessages($keywords);
                    if ($mails->isEmpty()) {
                        continue;
                    }
                    foreach ($mails as $mail) {
                        foreach ($boardIds as $boardId) {
                            $task = app('TaskRepo')->create(
                                [
                                    'priority_id' => null,
                                    'creator_id'  => null,
                                    'budget_id'   => null,
                                    'name'        => $mail->getSubject(),
                                    'description' => $mail->getTextBody(),
                                    'draft'       => 0,
                                    'deadline'    => null,
                                    'done_by'     => null,
                                ]
                            );
                            $task->board()->attach($boardId);
                        }
                        $mail->move('INBOX.'.self::OC_FOLDER);
                    }
                } catch (ValidatorException $e) {
                    continue;
                } catch (ConnectionFailedException $e) {
                    continue;
                } catch (GetMessagesFailedException $e) {
                    continue;
                } catch (MessageSearchValidationException $e) {
                    //Log::debug('Exception', ['Exception' => $e]);
                    continue;
                }
            }
            $client->disconnect();
        }
    }
}
