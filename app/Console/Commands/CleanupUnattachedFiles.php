<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use App\Services\Attachment\AttachmentService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanupUnattachedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:unattched_files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup database and filesystem from files which were not attached to comments';

    /**
     * @var AttachmentService
     */
    private $attachmentService;

    /**
     * CleanupUnattachedFiles constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->attachmentService = app(AttachmentService::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $attachments = Attachment::query()
            ->doesntHave('comments')
            ->where('created_at', '<', Carbon::now()->addDays(-1))
            ->get();

        $attachments->each(function(Attachment $attachment) {
            $this->attachmentService->delete($attachment);
        });
    }
}
