<?php

namespace App\Services\Pipeline;

use App\ImapClient;
use App\Services\BaseService;
use Illuminate\Support\Facades\Log;
use Webklex\IMAP\Exceptions\ConnectionFailedException;
use Webklex\IMAP\Exceptions\GetMessagesFailedException;

/**
 * Class PipelineService
 *
 * @package App\Services\Pipeline
 *
 * @author  LexXxurio
 */
class PipelineService extends BaseService
{
    /**
     * Return one mail pipeline with the passed id.
     *
     * @param int $pipelineId
     *
     * @return mixed
     */
    public function getPipelineById(int $pipelineId)
    {
        return app('PipelineRepo')->find($pipelineId);
    }

    /**
     * Return the pipeline rule with the passed id.
     *
     * @param int $pipelineRuleId
     *
     * @return mixed
     */
    public function getPipelineRuleById(int $pipelineRuleId)
    {
        return app('PipelineRuleRepo')->find($pipelineRuleId);
    }

    /**
     * Return all mail pipelines of one tenant.
     *
     * @param int $tenantId
     *
     * @return mixed
     */
    public function getPipelinesByTenantId(int $tenantId)
    {
        return app('PipelineRepo')->with(['rules.boards','rules.filter'])->findWhere(['tenant_id' => $tenantId]);
    }

    /**
     * Retrieve all pipeline filters.
     *
     * @return mixed
     */
    public function getPipelineFilters()
    {
        return app('PipelineFilterRepo')->findWhere(['is_active' => 1]);
    }

    /**
     * Create a mail pipeline with the passed options.
     *
     * @param array $attributes
     * @param int   $tenantId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createPipeline(array $attributes, int $tenantId)
    {
        $attributes['tenant_id'] = $tenantId;

        return app('PipelineRepo')->create($attributes);
    }

    /**
     * Create a pipeline rule with the passed options
     * and connect the chosen boards with it.
     *
     * @param array $boards
     * @param array $options
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createPipelineRule(array $boards, array $options)
    {
        $pipelineRule = app('PipelineRuleRepo')->create($options);
        $pipelineRule->boards()->attach($boards);
        return $pipelineRule;
    }

    /**
     * Update a mail pipeline with the passed options.
     *
     * @param array $attributes
     * @param int   $pipelineId
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updatePipeline(array $attributes, int $pipelineId)
    {
        app('PipelineRepo')->update($attributes, $pipelineId);
    }

    /**
     * Update a pipeline rule with the passed options and boards.
     *
     * @param int   $pipelineRuleId
     * @param array $boards
     * @param array $options
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updatePipelineRule(int $pipelineRuleId, array $boards, array $options)
    {
        //Update connection to the boards
        app('PipelineRuleRepo')->sync($pipelineRuleId, 'boards', $boards);

        //Update all other options
        app('PipelineRuleRepo')->update($options, $pipelineRuleId);
    }

    /**
     * Delete a mail pipeline.
     *
     * @param int $pipelineId
     *
     * @return bool
     */
    public function destroyPipeline(int $pipelineId): bool
    {
        return app('PipelineRepo')->delete($pipelineId);
    }

    /**
     * Delete a pipeline rule.
     *
     * @param int $pipelineRuleId
     *
     * @return bool
     */
    public function destroyPipelineRule(int $pipelineRuleId): bool
    {
        return app('PipelineRuleRepo')->delete($pipelineRuleId);
    }

    /**
     * Connect to the mailbox.
     *
     * @param int $pipelineId
     *
     * @return ImapClient
     */
    public function getMailClientForPipeline(int $pipelineId)
    {
        $pipeline = app('PipelineRepo')->find($pipelineId);

        return new ImapClient([
            'host'          => $pipeline->host,
            'port'          => $pipeline->port,
            'encryption'    => $pipeline->encryption !== 'none'? $pipeline->encryption : false,
            'validate_cert' => true,
            'username'      => $pipeline->email,
            'password'      => $pipeline->password
        ]);
    }

    /**
     * Retrieve mails from a mailbox.
     *
     * @param int $pipelineId
     *
     * @return array|\Webklex\IMAP\Support\MessageCollection
     */
    public function getMailsByPipelineId($pipelineId)
    {
        $messages = [];
        $client   = $this->getMailClientForPipeline($pipelineId);
      //  Log::debug('In getMailsByPipelineID', ['client' => $client]);
        try {

            //Connect to the IMAP Server
            $client->connect();

            //Get the Inbox Mailbox
            $inboxFolder = $client->getFolder('INBOX');

            $client->openFolder($inboxFolder);
            if ($client->countMessages() > 0) {

                //Get all Messages of the INBOX Mailbox
                /** @var \Webklex\IMAP\Support\MessageCollection $inboxMessages */
                $inboxMessages = $inboxFolder->getMessages();

                $i = 0;
                /** @var \Webklex\IMAP\Message $inboxMessage */
                foreach ($inboxMessages as $inboxMessage) {

                    $message             = [];
                    $message['id']       = $i;

                    //Header
                    $from                = $inboxMessage->getFrom();
                    $message['from']     = $from[0]->mail;
                    $message['subject']  = $inboxMessage->getSubject();

                    //Content
                    if ($inboxMessage->hasTextBody()) {
                        $message['body'] = $inboxMessage->getTextBody();
                    } else {
                        $message['body'] = $inboxMessage->getHTMLBody();
                    }

                    //Attachment
                    if (!empty($inboxMessage->attachments)) {

                        $attachments = $inboxMessage->getAttachments();
                        foreach ($attachments as $key => $attachment) {
                            $message['attachments'][$key]['name'] = $attachment->getName();
                            $message['attachments'][$key]['type'] = $attachment->getType();
                        }
                    }

                    $messages[] = $message;
                    $i++;
                }
            } else {
                $messages['error']['message'] = 'No new mails are available right now.';
                $messages['error']['code']    = 404;
            }
        } catch (ConnectionFailedException $ex) {
            $messages['error']['message'] = 'Couldn\'t connect to the mailbox.';
        } catch (GetMessagesFailedException $ex) {
            $messages['error']['message'] = 'Couldn\'t get the mails from the mailbox.';
        } catch (\Exception $ex) {
            Log::error('error:' . $ex->getCode() . ' ' . $ex->getMessage(), $ex->getTrace());
            $messages['error']['message'] = 'Error, check logs';
            $messages['error']['code']    = 500;
        }

        return array_reverse($messages);
    }
}
