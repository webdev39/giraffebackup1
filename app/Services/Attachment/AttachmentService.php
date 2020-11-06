<?php

namespace App\Services\Attachment;


use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

/**
 * Class AttachmentService
 * @package App\Services\Attachment
 */
class AttachmentService
{
    /**
     * @param Attachment $attachment
     * @throws \Exception
     */
    public function delete(Attachment $attachment)
    {
        Storage::delete($attachment->path);
        $attachment->delete();
    }
}