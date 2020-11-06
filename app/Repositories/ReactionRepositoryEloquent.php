<?php

namespace App\Repositories;

use App\Models\BaseModel;
use App\Models\Reactions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class ReactionsRepositoryEloquent
 *
 * @package App\Repositories
 * @property $model Reactions
 */
class ReactionRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Reactions::class;
    }

    /**
     * @param int   $userTenantId
     * @param Model $target
     * @param int   $targetId
     *
     * @return mixed
     */
    public function getLikesByTargetId(int $userTenantId, Model $target, int $targetId)
    {
        return $this->findByReactionAndTarget(Reactions::LIKE, $userTenantId, $target, $targetId);
    }

    /**
     * @param string $target
     * @param array  $targetIds
     *
     * @return Collection
     */
    public function getReactionsByTargets(string $target, array $targetIds) : Collection
    {
        return $this->findByTargetIds($target, $targetIds);
    }

    /**
     * @param int   $userTenantId
     * @param Model $target
     *
     * @return int|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function toggleLike(int $userTenantId, Model $target)
    {
        return $this->toggle(Reactions::LIKE, $userTenantId, $target);
    }

    /**
     * @param int   $userTenantId
     * @param Model $target
     * @param Model $source
     *
     * @return int|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function toggleStick(int $userTenantId, Model $target, Model $source = null)
    {
        return $this->toggle(Reactions::STICK, $userTenantId, $target, $source);
    }

    /**
     * @param string     $reaction
     * @param int        $userTenantId
     * @param Model      $target
     * @param Model|null $source
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    private function attach(string $reaction, int $userTenantId, Model $target, Model $source = null)
    {
        return $this->create(self::getAttributes($reaction, $userTenantId, $target, $source));
    }

    /**
     * @param string     $reaction
     * @param int        $userTenantId
     * @param Model      $target
     * @param Model|null $source
     *
     * @return int
     */
    private function detach(string $reaction, $userTenantId, Model $target, Model $source = null)
    {
        return $this->deleteWhere(self::getAttributes($reaction, $userTenantId, $target, $source));
    }

    /**
     * @param string $reaction
     * @param int    $userTenantId
     * @param Model  $target
     * @param Model  $source
     *
     * @return int|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    private function toggle(string $reaction, int $userTenantId, Model $target, Model $source = null)
    {
        if ($this->findModel($reaction, null, $target, $source)) {
            return $this->detach($reaction, null, $target, $source);
        }

        return $this->attach($reaction, $userTenantId, $target, $source);
    }

    /**
     * @param string     $reaction
     * @param int        $userTenantId
     * @param Model      $target
     * @param Model|null $source
     *
     * @return mixed
     */
    private function findModel(string $reaction, $userTenantId, Model $target, Model $source = null)
    {
        return $this->findWhere(self::getAttributes($reaction, $userTenantId, $target, $source))->first();
    }

    /**
     * @return mixed
     */
    private function findReactionBuilder()
    {
        return $this->model
            ->select(
                'targetable_id   as target_id',
                'targetable_id   as target_model',
                'sourceable_id   as source_id',
                'sourceable_type as source_model',
                'reaction',
                'user_tenant_id'
            );
    }

    /**
     * @param string $reaction
     * @param int    $userTenantId
     * @param Model  $target
     * @param int    $id
     *
     * @return mixed
     */
    private function findByReactionAndTarget(string $reaction, int $userTenantId, Model $target, int $id)
    {
        return $this->findReactionBuilder()
            ->where('targetable_id', $id)
            ->where('targetable_type', self::getModelName($target))
            ->where('user_tenant_id', $userTenantId)
            ->where('reaction', $reaction)
            ->get();
    }

    /**
     * @param string $target
     * @param array  $targetIds
     *
     * @return mixed
     */
    private function findByTargetIds(string $target, array $targetIds)
    {
        return $this->findReactionBuilder()
            ->whereIn('targetable_id', $targetIds)
            ->where('targetable_type', self::getModelName($target))
            ->get();
    }

    /**
     * @param string $reaction
     * @param int    $userTenantId
     * @param Model  $target
     * @param Model  $source
     *
     * @return array
     */
    private static function getAttributes(string $reaction, $userTenantId, Model $target, Model $source = null)
    {
        $attributes = [
            'targetable_id'     => $target->id,
            'targetable_type'   => self::getModelName($target),
            'reaction'          => $reaction,
        ];
        if(!is_null($userTenantId)) {
            $attributes['user_tenant_id'] = $userTenantId;
        }

        if ($source) {
            $attributes['sourceable_id']   = $source->id;
            $attributes['sourceable_type'] = self::getModelName($source);
        }

        return $attributes;
    }

    /**
     * @param BaseModel|string $model
     *
     * @return string
     */
    private static function getModelName($model) : string
    {
        if ($model instanceof Model) {
            $model = $model->getMorphClass();
        }

        return strtolower(class_basename($model));
    }
}
