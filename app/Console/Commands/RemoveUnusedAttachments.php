<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use Illuminate\Console\Command;

class RemoveUnusedAttachments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:unused-attachments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove unused attachments';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $attachments = app('AttachmentRepo')->getUnusedAttachments();
        $attachmentIds = $attachments->pluck('id')->toArray();

        $count = Attachment::whereIn('id', $attachmentIds)->delete();

        $this->info("It was deleted: {$count} records");
    }
}
