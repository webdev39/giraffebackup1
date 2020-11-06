<?php
/**
 * Created by PhpStorm.
 * User: nikolaygolub
 * Date: 21.12.2017
 * Time: 15:23
 */

namespace App\Services\Priority;

use App\Models\Board;
use App\Models\Priority;
use App\Services\BaseService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PriorityService extends BaseService
{
    /** @var \App\Repositories\PriorityRepositoryEloquent */
    private $priorityRepo;

    /**
     * PriorityService constructor.
     */
    public function __construct()
    {
        $this->priorityRepo = app('PriorityRepo');
    }

    /**
     * @return mixed
     */
    public function getDefaultPriority()
    {
        return $this->priorityRepo->findWhere(['is_default' => true]);
    }

    /**
     * @param int $userTenantId
     *
     * @return mixed
     */
    public function getPrioritiesByUserTenantId(int $userTenantId)
    {
        return $this->priorityRepo->getPrioritiesByUserTenantId($userTenantId);
    }

    /**
     * @param array $boardIds
     * @param int   $userTenantId
     *
     * @return Collection
     */
    public function getPrioritiesByBoardIds(array $boardIds, int $userTenantId) : Collection
    {
        return $this->priorityRepo->getPrioritiesByBoardIds($boardIds, $userTenantId);
    }

    /**
     * @param int $boardId
     * @param int $userTenantId
     *
     * @return Collection
     */
    public function getPrioritiesByBoardId(int $boardId, int $userTenantId) : Collection
    {
        return $this->getPrioritiesByBoardIds([$boardId], $userTenantId);
    }

    /**
     * @param int $boardId
     *
     * @return \stdClass
     */
    public function getPrimaryPriorityByBoardId(int $boardId)
    {
        return $this->priorityRepo->getPrimaryPriorityByBoardId($boardId);
    }

    /**
     * @param array $priorityIds
     *
     * @return mixed
     */
    public function getPriorityModelsByIds(array $priorityIds)
    {
        return $this->priorityRepo->findWhereIn('id', $priorityIds);
    }

    /**
     * @param int $priorityId
     *
     * @return mixed
     */
    public function getPriorityModelById(int $priorityId)
    {
        return $this->priorityRepo->find($priorityId);
    }


    public function getPriorityWithRelationsById(int $priorityId, int $userTenantId)
    {
        return $this->priorityRepo->getPriorityWithRelationsById($priorityId, $userTenantId);
    }

    /**
     * @param int $priorityId
     *
     * @return bool|null
     * @throws \Exception
     */
    public function remove(int $priorityId)
    {
        return $this->priorityRepo->delete($priorityId);
    }

    /**
     * @param int $boardId
     *
     * @return int
     */
    public function removePriorityByBoardId(int $boardId)
    {
        return $this->priorityRepo->removePriorityByBoardIds([$boardId]);
    }

    /**
     * @param int $boardId
     *
     * @throws \Throwable
     */
    public function createUniqDefaultPriorities(int $boardId)
    {
        /** @var Collection $boards */
        $priorities = $this->getDefaultPriority();

        DB::transaction(function () use ($boardId, $priorities) {
            $priorities->each(function($priority) use ($boardId) {
                $this->createOrUpdatePriority([
                    'board_id'   => $boardId,
                    'name'       => $priority->name,
                    'color'      => $priority->color,
                    'is_primary' => $priority->is_primary,
                ]);
            });
        });
    }

    /**
     * @param array    $attributes
     * @param int|null $priorityId
     *
     * @return mixed
     */
    public function createOrUpdatePriority(array $attributes, int $priorityId = null)
    {
        if ($priorityId) {
            return app('PriorityRepo')->update($attributes, $priorityId);
        } else {
            return app('BoardRepo')->find($attributes['board_id'])->customPriorities()->create($attributes);
        }
    }

    /**
     * @param int  $priorityId
     * @param int  $userTenantId
     * @param bool $isInvisible
     *
     * @return mixed
     */
    public function updateInvisible(int $priorityId, int $userTenantId, bool $isInvisible)
    {
        return app('UserTenantPriorityRepo')->updateInvisible($priorityId, $userTenantId, $isInvisible);
    }

    /**
     * @param Priority $priority
     * @param int|null $boardId
     *
     * @return bool
     */
    public function checkPriorityInBoard(Priority $priority, int $boardId = null)
    {
        return $priority->board()->first()->id == $boardId;
    }

    /**
     * @param array $orders
     */
    public function updateTaskOrders(array $orders)
    {
        $this->priorityRepo->updateTaskOrders($orders);
    }
}
