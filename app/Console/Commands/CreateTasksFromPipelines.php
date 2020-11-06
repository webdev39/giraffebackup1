<?php

namespace App\Console\Commands;

use App\ImapClient;
use App\Models\Pipeline;
use App\Models\PipelineRule;
use App\Notifications\PipelineDeactivatedNotification;
use App\Repositories\PipelineRepositoryEloquent;
use App\Services\Pipeline\PipelineService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Prettus\Validator\Exceptions\ValidatorException;
use Webklex\IMAP\Exceptions\ConnectionFailedException;
use Webklex\IMAP\Exceptions\GetMessagesFailedException;
use Webklex\IMAP\Exceptions\MessageSearchValidationException;

/**
 * Class CreateTasksFromPipelines
 *
 * @package App\Jobs
 *
 * @author  LexXurio
 */
class CreateTasksFromPipelines extends Command
{
    
    protected $signature = 'pipelines:run';
    
    const OC_FOLDER = '*OC Tasks';
    
    /**
     * @var PipelineService
     */
    private $service;
    /**
     * @var PipelineRepositoryEloquent
     */
    private $pipelineRepository;
    
    /**
     * CreateTasksFromPipelines constructor.
     * @param PipelineService $service
     * @param PipelineRepositoryEloquent $pipelineRepository
     */
    public function __construct(PipelineService $service, PipelineRepositoryEloquent $pipelineRepository)
    {
        parent::__construct();
        $this->service = $service;
        $this->pipelineRepository = $pipelineRepository;
    }
    
    /**
     * @throws \ErrorException
     */
    public function handle()
    {
        $pipelines = $this->pipelineRepository->all()->where('is_active', 1);
        
        /** @var Pipeline $pipeline */
        foreach ($pipelines as $pipeline) {
            /** @var ImapClient $client */
            $client = $this->service->getMailClientForPipeline($pipeline->id);
            $client->validate_cert = false;
            
            try {
                $client->pingDomain($client->host, $client->port);
                $client->connect();
    
                if (!$client->folderExists(self::OC_FOLDER)) {
                    $client->createFolder('{imap.' . $pipeline->host . ':' . $pipeline->port . '}INBOX.' . $this->getCleanFolderName());
                }
    
                /** @var \Webklex\IMAP\Folder $inboxFolder */
                $inboxFolder = $client->getFolder('INBOX');
    
                /** @var PipelineRule[] $distRules */
                $distRules = $pipeline->rules;
                foreach ($distRules as $distRule) {
                    $boardIds = [];
                    /** @var \App\Models\Board $board */
                    foreach ($distRule->boards as $board) {
                        $boardIds[] = $board->id;
                    }
        
                    $keywords = $distRule->getKeywords();
                    /** @var \Webklex\IMAP\Support\MessageCollection $mails */
                    $mails = $inboxFolder->searchMessages($keywords);
                    if ($mails->isEmpty()) {
                        $this->info('No message found');
                        continue;
                    }
        
                    /** @var \Webklex\IMAP\Message $mail */
                    foreach ($mails as $mail) {
                        foreach ($boardIds as $boardId) {
                            $task = app('TaskRepo')->create(
                                [
                                    'priority_id' => null,
                                    'creator_id' => null,
                                    'budget_id' => null,
                                    'name' => $mail->getSubject(),
                                    'description' => $mail->getTextBody(),
                                    'draft' => 0,
                                    'deadline' => null,
                                    'done_by' => null,
                                ]
                            );
                            $task->board()->attach($boardId);
                        }
                        $mailbox = 'INBOX.' . $this->getCleanFolderName();
                        imap_mail_move($client->getConnection(), $mail->msglist, $mailbox);
                    }
                }
            } catch (ValidatorException $e) {
                continue;
            } catch (ConnectionFailedException $e) {
                $this->warn($e->getMessage());
                continue;
            } catch (GetMessagesFailedException $e) {
                continue;
            } catch (MessageSearchValidationException $e) {
                $this->warn($e->getMessage());
                continue;
            } catch (\ErrorException $e) {
                $this->warn($e->getMessage());
//                Notification::send([$pipeline->tenant->owner()], new PipelineDeactivatedNotification($e->getMessage()));
                $pipeline->deactivate();
            }
            
            $client->disconnect();
        }
        
        $this->info('all done');
    }
    
    private function getCleanFolderName(): string
    {
        return str_replace('*', '', static::OC_FOLDER);
    }
    
}
