<?php

namespace App\Http\Controllers\Api;

use App\Events\CreatedCommentEvent;
use App\Http\Requests\CreateAttachmentCommentRequest;
use App\Http\Requests\SaveFileRequest;
use App\Http\Requests\UpdateAttachmentCommentRequest;
use App\Http\Resources\AttachmentResource;
use App\Http\Resources\CommentResource;
use App\Models\Permission;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserTenant;
use App\Services\Comment\CommentService;
use App\Services\Task\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AttachmentController extends Controller
{
    /** @var CommentService */
    private $commentService;

    /** @var TaskService */
    private $taskService;

    /**
     * AttachmentController constructor.
     */
    public function __construct()
    {
        $this->commentService   = app('CommentSer');
        $this->taskService      = app('TaskSer');
    }

    /**
     * @param int $attachmentId
     *
     * @return JsonResponse
     */
    public function showComments(int $attachmentId) : JsonResponse
    {
        $comments = $this->commentService->getAttachmentCommentsByAttachmentId($attachmentId);

        return response()->json([
            'comments' => CommentResource::collection($comments)
        ]);
    }

    /**
     * @param $request
     *
     * @return JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createComment(CreateAttachmentCommentRequest $request) : JsonResponse
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();
        /** @var User $user */
        $user       = Auth::user();
        $task       = $this->taskService->getTaskById($request->get('taskId'));

        $user->can('createComment', [$task, $userTenant]);

        $comment = $this->commentService->createOrUpdateComment([
            'task_id'           => $task->id,
            'user_id'           => $userTenant->user_id,
            'body'              => $request->get('body'),
        ]);

        if ($request->get('mentions')) {
            event(new CreatedCommentEvent($user, $request->get('mentions'), $comment->body, $task->group_id, $task));
        }

        $this->commentService->attachToAttachment($comment->id, $request->get('attachmentId'), $request->get('spatial'));

        $comment = $this->commentService->getAttachmentCommentById($comment->id);

        return response()->json([
            'comment' => new CommentResource($comment)
        ]);
    }

    /**
     * @param UpdateAttachmentCommentRequest $request
     *
     * @return JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateComment(UpdateAttachmentCommentRequest $request) : JsonResponse
    {
        $user       = Auth::user();
        $comment    = $this->commentService->getCommentById($request->get('commentId'));

        $user->can('update', $comment);

        $comment = $this->commentService->createOrUpdateComment([
            'body' => $request->get('body'),
        ], $comment->id);

        if ($request->get('mentions')) {
            $task = $this->taskService->getTaskById($comment->task_id);

            event(new CreatedCommentEvent($user, $request->get('mentions'), $comment->body, $task->group_id, $task));
        }

        $comment = $this->commentService->getAttachmentCommentById($comment->id);

        return response()->json([
            'comment' => new CommentResource($comment)
        ]);
    }

    /**
     * @param int $attachmetId
     * @param int $commentId
     *
     * @return JsonResponse
     */
    public function destroyComment(int $attachmetId, int $commentId) : JsonResponse
    {
        $comment = $this->commentService->getCommentById($commentId);

        Auth::user()->can('delete', $comment);

        $this->commentService->removeComment($comment->id);

        return response()->json([
            'message' => 'Comment has been removed successfully'
        ]);
    }

    /**
     * @param SaveFileRequest $request
     *
     * @return JsonResponse
     */
    public function upload(SaveFileRequest $request)
    {
        $attachments = $request->all('attachment');
        $comments = $request->input('comments', []);
        $createdAttachment = [];

        if (count($attachments['attachment'])) {
            foreach ($attachments['attachment'] as $i => $attachment) {
                $comment = '';
                if(array_key_exists($i, $comments)) {
                    $comment = $comments[$i];
                }
                $createdAttachment[] = $this->commentService->saveAttachment($attachment, Auth::user(), $comment);
            }

            return response()->json([
                'attachment' => AttachmentResource::collection(collect($createdAttachment))
            ]);
        }

        return abort(404, 'Choose the file');
    }
}
