<?php

namespace App\Services\Comment;

use App\Collections\CommentCollection;
use App\Collections\UserCollection;
use App\Events\Eloquent\Saved\SavedCommentEvent;
use App\Models\Attachment;
use App\Models\AttachmentComment;
use App\Models\Comment;
use App\Models\User;
use App\Repositories\CommentAttachmentRepositoryEloquent;
use App\Repositories\CommentRepositoryEloquent;
use App\Services\BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Prettus\Validator\Exceptions\ValidatorException;
use Spatie\Image\Image;

class CommentService extends BaseService
{
    /** Directory for save files */
    const DIRECTORY = 'comment';

    /** @var CommentRepositoryEloquent */
    private $commentRepo;

    /** @var CommentAttachmentRepositoryEloquent */
    private $commentAttachmentRepo;

    /**
     * CommentService constructor.
     */
    public function __construct()
    {
        $this->commentRepo              = app('CommentRepo');
        $this->commentAttachmentRepo    = app('CommentAttachmentRepo');
    }

    /**
     * @param int $attachmentId
     *
     * @return mixed
     */
    public function getAttachmentById(int $attachmentId)
    {
        return app('AttachmentRepo')->find($attachmentId);
    }

    /**
     * @param string|null $fileExtension
     *
     * @return bool
     */
    public static function getClientIsImage(string $fileExtension = null)
    {
        return in_array(strtolower($fileExtension), ['jpg', 'png', 'gif', 'bmp', 'jpeg', 'gif']);
    }

    /**
     * @param array    $attributes
     * @param int|null $commentId
     *
     * @return mixed
     * @throws ValidatorException
     */
    public function createOrUpdateComment(array $attributes, int $commentId = null)
    {
        $comment = $this->commentRepo->updateOrCreate(['id' => $commentId], array_merge($attributes, [
            'is_edited' => $commentId !== null,
        ]));

        if (isset($attributes['parent_id']) && $attributes['parent_id']) {
            $parent = $this->commentRepo->find($attributes['parent_id']);
            $parent->appendNode($comment);
        }

        if (isset($attributes['attachments_id']) && $attributes['attachments_id']) {
            $this->updateCommentAttachmentLinks($comment->id, $attributes['attachments_id']);
        }

        event(new SavedCommentEvent($comment));

        return $comment;
    }

    /**
     * @param int   $commentId
     * @param array $attachmentIds
     *
     * @throws ValidatorException
     */
    public function updateCommentAttachmentLinks(int $commentId, $attachmentIds = [])
    {
        $commentAttachmentIds = $this->commentRepo->find($commentId)->attachments->pluck('id')->all();

        foreach (array_diff($attachmentIds, $commentAttachmentIds) as $attachmentId) {
            $this->createCommentAttachmentLink($commentId, $attachmentId);
        }

        foreach (array_diff($commentAttachmentIds, $attachmentIds) as $attachmentId) {
            $this->removeCommentAttachmentLink($commentId, $attachmentId);
        }
    }

    /**
     * @param int $commentId
     * @param     $attachmentId
     *
     * @return mixed
     * @throws ValidatorException
     */
    public function createCommentAttachmentLink(int $commentId, $attachmentId)
    {
        return $this->commentAttachmentRepo->create([
            'comment_id'    => $commentId,
            'attachment_id' => $attachmentId
        ]);
    }

    /**
     * @param int $commentId
     * @param     $attachmentId
     *
     * @return int
     */
    public function removeCommentAttachmentLink(int $commentId, $attachmentId)
    {
        return $this->commentAttachmentRepo->deleteWhere([
            'comment_id'    => $commentId,
            'attachment_id' => $attachmentId
        ]);
    }

    /**
     * @param int $commentId
     *
     * @return int
     */
    public function removeComment(int $commentId)
    {
        return $this->commentRepo->delete($commentId);
    }

    /**
     * @param UploadedFile $attachment
     * @param User $user
     * @param string|null $comment
     * @return Attachment|\Illuminate\Database\Eloquent\Model
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function saveAttachment(UploadedFile $attachment, User $user, ?string $comment)
    {
        $oldPath = 'storage'. DIRECTORY_SEPARATOR . Storage::disk(config('filesystems.default'))->put(self::DIRECTORY, $attachment);
        $path = $oldPath;

        $fileExtension = $attachment->getClientOriginalExtension();
        if($isImage = self::getClientIsImage($fileExtension)) {
            $fileInfo = pathinfo($path);
            $newFileName = $fileInfo['filename'] . '-thumb.' . $fileInfo['extension'];
            $pathToNewImage = Storage::disk(config('filesystems.default'))->path(self::DIRECTORY) . DIRECTORY_SEPARATOR . $newFileName;
            Image::load($path)
                ->width(550)
                ->height(550)
                ->save($pathToNewImage);
            $path = 'storage' . DIRECTORY_SEPARATOR . self::DIRECTORY . DIRECTORY_SEPARATOR . $newFileName;
        }

        return Attachment::create([
            'path'          => DIRECTORY_SEPARATOR . $oldPath,
            'path_new'      => DIRECTORY_SEPARATOR . $path,
            'creator_id'    => $user->id,
            'name'          => $attachment->getClientOriginalName(),
            'size'          => $attachment->getClientSize(),
            'is_image'      => $isImage,
            'comment'       => (string) $comment
        ]);
    }

    /**
     * @param int $commentId
     * @param bool $loadTask
     * @return mixed
     */
    public function getCommentById(int $commentId, bool $loadTask = true)
    {
        /** @var CommentCollection $comments */
        $comments = $this->commentRepo->getCommentByIds([$commentId], false, $loadTask);
        $comments = $this->addCommentRelations($comments);

        return $comments->first();
    }

    /**
     * @param int $commentId
     *
     * @return mixed
     * @throws \Exception
     */
    public function getCommentWithDescendantById(int $commentId)
    {
        /** @var CommentCollection $comments */
        $comments = $this->commentRepo->getCommentByIds([$commentId]);
        $comments = $this->addCommentRelations($comments, true);

        $comment = $comments->first();

        if (!$comment) {
            throw new \Exception('Comment not found', 404);
        }

        $descendantIds = $comment->descendants->pluck(['id'])->toArray();

        $comment->descendants = $this->commentRepo->getCommentByIds($descendantIds, false);
        $comment->descendants = $this->addCommentRelations($comment->descendants);

        return $comment;
    }

    public function getLastReplies(int $commentId, int $repliesCount = 3)
    {
        $replies = Comment::where('parent_id', $commentId)
            ->where('created_at', '>=', now()->startOfDay()->addDays(-3))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        if($replies->count() > 4) {
            $replies = $replies->slice(0, 3);
        }
        return $replies;
    }

    /**
     * @param int $taskId
     * @param     $userTenantId
     *
     * @return Collection
     */
    public function getCommentsByTaskId(int $taskId, int $userTenantId = null) : Collection
    {
        $comments = $this->commentRepo->getCommentsByTaskId($taskId, $userTenantId);
        $comments = $this->addCommentRelations($comments);

        return $comments;
    }

    /**
     * @param int $attachmentId
     *
     * @return Collection
     */
    public function getAttachmentCommentsByAttachmentId(int $attachmentId): Collection
    {
        $comments = $this->commentRepo->getAttachmentCommentsByAttachmentId($attachmentId);
        $comments = $this->addCommentRelations($comments);

        return $comments;
    }

    /**
     * @param int $commentId
     *
     * @return \stdClass
     */
    public function getAttachmentCommentById(int $commentId): \stdClass
    {
        $comments = $this->commentRepo->getAttachmentCommentById($commentId);
        $comments = $this->addCommentRelations($comments);

        return $comments->first();
    }

    /**
     * @param Collection $comments
     * @param bool       $setDescendants
     *
     * @return Collection
     */
    public function addCommentRelations(Collection $comments, $setDescendants = false) : Collection
    {
        $commentIds     = $comments->pluck('id')->unique()->toArray();
        $userIds        = $comments->pluck('user_id')->unique()->toArray();

        $attachments    = app('AttachmentRepo')->getAttachmentIdsByCommentIds($commentIds);
        $descendants    = app('CommentRepo')->getDescendantsAndSelf($commentIds);
        $reactions      = app('ReactionRepo')->getReactionsByTargets('comment', $commentIds);
        $users          = app('UserRepo')->getUsersByIds($userIds);

        $comments = CommentCollection::make($comments);
        $comments->setAttributes([
            'attachments' => $attachments->groupBy('comment_id'),
            'reactions'   => $reactions->groupBy('target_id'),
        ]);

        if ($setDescendants) {
            $comments->setDescendants($descendants);
        }

        $users = UserCollection::make($users);
        $comments->setUser($users);

        return $comments;
    }


    /**
     * @param int        $commentId
     * @param int        $attachmentId
     * @param array|null $spatial
     *
     * @return mixed
     * @throws ValidatorException
     */
    public function attachToAttachment(int $commentId, int $attachmentId, array $spatial = null)
    {
        return app('AttachmentCommentRepo')->create([
            'comment_id'     => $commentId,
            'attachment_id'  => $attachmentId,
            'spatial'        => $spatial,
        ]);
    }
}
