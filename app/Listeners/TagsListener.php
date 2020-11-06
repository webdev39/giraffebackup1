<?php

namespace App\Listeners;

use App\Events\Eloquent\Saved\SavedBoardEvent;
use App\Events\Eloquent\Saved\SavedCommentEvent;
use App\Events\Eloquent\Saved\SavedGroupEvent;
use App\Events\Eloquent\Saved\SavedTaskEvent;
use App\Services\TagService;

class TagsListener
{
    /**
     * @var TagService
     */
    private $tagService;

    /**
     * TagsListener constructor.
     * @param TagService $tagService
     */
    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * @param SavedTaskEvent $event
     */
    public function savedTask(SavedTaskEvent $event)
    {
        $task = $event->model;
        $this->tagService->attachTags($task, [$task->name, $task->description]);
    }

    /**
     * @param SavedBoardEvent $event
     */
    public function savedBoard(SavedBoardEvent $event)
    {
        $board = $event->model;
        $this->tagService->attachTags($board, [$board->name, $board->description]);
    }

    /**
     * @param SavedGroupEvent $event
     */
    public function savedGroup(SavedGroupEvent $event)
    {
        $group = $event->model;
        $this->tagService->attachTags($group, [$group->name, $group->description]);
    }

    /**
     * @param SavedCommentEvent $event
     */
    public function savedComment(SavedCommentEvent $event)
    {
        $comment = $event->model;
        $this->tagService->attachTags($comment, [$comment->body]);
    }
}
