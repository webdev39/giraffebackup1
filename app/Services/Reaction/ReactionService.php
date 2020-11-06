<?php

namespace App\Services\Reaction;

use App\Events\LikedCommentEvent;
use App\Events\StickCommentEvent;
use App\Models\Board;
use App\Models\Comment;
use App\Models\Group;
use App\Models\Task;
use App\Models\UserTenant;
use App\Notifications\CommentLikeNotification;
use App\Repositories\ReactionRepositoryEloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ReactionService
{
    /**
     * List of models to which the action is carried out
     */
    const TARGET_MODELS = [
        'comment' => Comment::class
    ];

    /**
     * List of models that are associated with action
     */
    const SOURCE_MODELS = [
        'task'  => Task::class,
        'board' => Board::class,
        'group' => Group::class,
    ];

    /**
     * @var ReactionRepositoryEloquent
     */
    public $reactionRepository;

    /**
     * ReactionService constructor.
     */
    public function __construct()
    {
        $this->reactionRepository = app('ReactionRepo');
    }

    /**
     * @param string $model
     * @param bool   $source
     *
     * @return Model
     * @throws \Exception
     */
    public static function getModel(string $model, bool $source = false) : Model
    {
        $class = $source ? self::SOURCE_MODELS[$model] : self::TARGET_MODELS[$model] ;

        if (!$class) {
            throw new \Exception('Target model not found', 500);
        }

        return new $class;
    }

    /**
     * @param string $model
     * @param int    $id
     * @param bool   $source
     *
     * @return Model
     * @throws \Exception
     */
    public static function findModel(string $model, int $id, bool $source = false) : Model
    {
        $model = self::getModel($model, $source)->find($id);

        if (!$model) {
            throw new \Exception("{$model} not found", 404);
        }

        return $model;
    }


    /**
     * @param string $target
     * @param int    $targetId
     * @param int    $userTenantId
     *
     * @return int|mixed
     * @throws \Exception
     */
    public function toggleLike(int $userTenantId, string $target, int $targetId)
    {
        $targetModel = self::findModel($target, $targetId);

        $isLikeToggled = $this->reactionRepository->toggleLike($userTenantId, $targetModel);

        if($targetModel instanceof Comment && $targetModel->isLikedBy(\Auth::user())) {
            event(new LikedCommentEvent(UserTenant::find($userTenantId)->user, $targetModel));
        }

        return $isLikeToggled;
    }

    /**
     * @param string $target
     * @param int    $targetId
     * @param string $source
     * @param int    $sourceId
     * @param int    $userTenantId
     *
     * @return int|mixed
     * @throws \Exception
     */
    public function toggleStick(int $userTenantId, string $target, int $targetId, string $source, int $sourceId)
    {
        $targetModel = self::findModel($target, $targetId);
        $sourceModel = self::findModel($source, $sourceId, true);

        $stick = $this->reactionRepository->toggleStick($userTenantId, $targetModel, $sourceModel);;

        if($targetModel instanceof Comment && $targetModel->isStickedBy(\Auth::user())) {
            event(new StickCommentEvent(UserTenant::find($userTenantId)->user, $targetModel));
        }

        return $stick;
    }

    /**
     * @param int    $userTenantId
     * @param string $target
     * @param int    $targetId
     *
     * @return mixed
     * @throws \Exception
     */
    public function likers(int $userTenantId, string $target, int $targetId)
    {
        $targetModel = self::getModel($target);

        return $this->reactionRepository->getLikesByTargetId($userTenantId, $targetModel, $targetId);
    }

    /**
     * @param string $target
     * @param array  $targetIds
     *
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    public function getReactionsByTargets(string $target, array $targetIds)
    {
        $targetModel = self::getModel($target);

        return $this->reactionRepository->getReactionsByTargets($targetModel, $targetIds);
    }
}
