<?php

namespace App\Http\Controllers\Api;

use App\Events\CreatedCommentEvent;
use App\Events\CreatedCommentCommunicationEvent;
use App\Events\Eloquent\ChangedTaskEvent;
use App\Events\Eloquent\Saved\DeletedCommentEvent;
use App\Events\GroupCommentPostedEvent;
use App\Http\Requests\CreateTaskCommentRequest;
use App\Http\Requests\UpdateTaskCommentRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Group;
use App\Models\Task;
use App\Models\UserTenant;
use App\Services\Comment\CommentService;
use App\Services\Task\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /** @var CommentService */
    private $commentService;

    /** @var TaskService */
    private $taskService;

    /**
     * CommentController constructor.
     */
    public function __construct()
    {
        $this->commentService   = app('CommentSer');
        $this->taskService      = app('TaskSer');
    }

    /**
     * @param int $commentId
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function show(int $commentId) : JsonResponse
    {
        $comment = $this->commentService->getCommentWithDescendantById($commentId);

        return response()->json([
            'comment' => new CommentResource($comment)
        ]);
    }

    /**
     * @param int $taskId
     *
     * @return JsonResponse
     */
    public function showTaskComments(int $taskId) : JsonResponse
    {
        $comments = $this->commentService->getCommentsByTaskId($taskId);

        return response()->json([
            'comments' => CommentResource::collection($comments)
        ]);
    }

    /**
     * @param CreateTaskCommentRequest $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(CreateTaskCommentRequest $request)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();
        $user = Auth::user();

        if($request->filled('taskId')) {

            $task = $this->taskService->getTaskById($request->get('taskId'));
            $group = $task->board->first()->group;

            $this->authorize('createComment', [$task]);

        } else {

            $group = Group::findOrFail($request->get('groupId'));
            $this->authorize('createComment', [$group]);

        }

        $comment = $this->commentService->createOrUpdateComment([
            'task_id'           => isset($task) ? $task->id : null,
            'user_id'           => $userTenant->user_id,
            'body'              => $request->get('body'),
            'attachments_id'    => $request->get('attachments'),
            'parent_id'         => $request->get('parentId'),
            'groupId'           => $group->id,
            'board_id'          => $request->get('boardId')
        ]);

        if ($request->filled('mentions')) {
            event(new CreatedCommentEvent($user, $request->get('mentions'), $comment->body, $group->id, $task ?? null));
        }

        $comment = $this->commentService->getCommentById($comment->id, !empty($task));

        if (!empty($task)) {
            event(new ChangedTaskEvent($task));
        }

        return response()->json([
            'comment' => new CommentResource($comment)
        ]);
    }

    /**
     * @param UpdateTaskCommentRequest $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(UpdateTaskCommentRequest $request)
    {
        $user       = Auth::user();
        $comment    = $this->commentService->getCommentById($request->get('commentId'));

        if ($comment->user_id != $user->id) {
            abort('Comment does not match user');
        }

        $comment = $this->commentService->createOrUpdateComment([
            'body'              => $request->get('body'),
            'attachments_id'    => $request->get('attachments'),
            'parent_id'         => $request->get('parentId')
        ], $comment->id);

        if(!empty($comment->task_id)) {
            $task = $this->taskService->getTaskById($comment->task_id);
            $this->authorize('update', [$comment, $task]);
        } else {
            $this->authorize('manage', [$comment]);
        }

        if ($request->get('mentions')) {
            event(new CreatedCommentEvent($user, $request->get('mentions'), $comment->body, $request->get('groupId'), $task ?? null));
        }

        $comment = $this->commentService->getCommentById($comment->id);

        if (!empty($task)) {
            event(new ChangedTaskEvent($task));
        }

        return response()->json([
            'comment' => new CommentResource($comment)
        ]);
    }

    /**
     * @param int $commentId
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(int $commentId)
    {
        $comment = Comment::findOrFail($commentId);

        if(!empty($comment->task_id)) {
            $task = $this->taskService->getTaskById($comment->task_id);
            $this->authorize('delete', [$comment, $task]);
        } else {
            $this->authorize('manage', [$comment]);
        }

        event(new DeletedCommentEvent($comment));

        $this->commentService->removeComment($comment->id);

        if (!empty($task)) {
            event(new ChangedTaskEvent($task));
        }

        return response()->json([
            'message' => 'Comment has been removed successfully'
        ]);
    }
}
