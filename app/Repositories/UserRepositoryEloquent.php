<?php

namespace App\Repositories;

use App\Models\NotificationSubscription;
use App\Models\PasswordReset;
use App\Models\Role;
use App\Models\User;
use App\Models\UserTenant;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class UserRepositoryEloquent extends BaseRepositoryEloquent
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * @param array $userIds
     *
     * @return Collection
     */
    /**
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function buildUserWithRelations()
    {
        return DB::table($this->userTable)
            ->select([
                $this->userTable.'.id',
                $this->userTable.'.name',
                $this->userTable.'.email',
                $this->userTable.'.last_name',
                $this->userTable.'.is_confirmed',
                $this->userTable.'.chosen_tenant_id',
                $this->userTable.'.last_activity',
                $this->userTable.'.nickname',
                $this->userTable.'.status',
                $this->userProfileTable.'.selected_color_scheme_id',
                $this->userProfileTable.'.language_id',
                $this->userProfileTable.'.font_id',
                $this->userProfileTable.'.primary_color',
                $this->userProfileTable.'.secondary_color',
                $this->userProfileTable.'.time_zone',
                $this->userProfileTable.'.tour',
                $this->userProfileTable.'.audio',
                $this->userTenantTable.'.id             as user_tenant_id',
                $this->userTenantTable.'.tenant_id      as tenant_id',
                $this->userTenantTable.'.is_owner       as is_owner',
                $this->userTenantTable.'.can_invited       as can_invited',
                'avatar_table.url                       as avatar',
                'background_table.url                   as background',
            ])
            ->leftJoin($this->userTenantTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->userTable.'.id', $this->userTenantTable.'.user_id');
            })
            ->leftJoin($this->userProfileTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->userProfileTable.'.user_id', $this->userTable.'.id');
            })
            ->leftJoin($this->imageTable.' as avatar_table', function ($join) {
                /** @var $join JoinClause */
                $join->on('avatar_table.id', $this->userProfileTable.'.avatar_id');
            })
            ->leftJoin($this->imageTable.' as background_table', function ($join) {
                /** @var $join JoinClause */
                $join->on('background_table.id', $this->userProfileTable.'.background_id');
            });
    }

    /**
     * @param int $userId
     *
     * @return object
     */
    public function getUserById(int $userId)
    {
        return $this->buildUserWithRelations()
            ->where($this->userTable.'.id', $userId)
            ->first();
    }

    /**
     * @param array $userIds
     *
     * @return Collection
     */
    public function getUsersByIds(array $userIds) : Collection
    {
        return $this->buildUserWithRelations()
            ->whereIn($this->userTable.'.id', $userIds)
            ->get();
    }

    /**
     * @param string $email
     *
     * @return \stdClass
     */
    public function getUserByEmail(string $email) : \stdClass
    {
        return $this->buildUserWithRelations()
            ->where($this->userTable.'.email', $email)
            ->first();
    }

    /**
     * @param string $confirmHash
     *
     * @return \stdClass
     */
    public function getUserByConfirmHash(string $confirmHash) : ?\stdClass
    {
        return $this->buildUserWithRelations()
            ->where($this->userTable.'.confirm_hash', $confirmHash)
            ->where($this->userTable.'.is_confirmed', 0)
            ->get()
            ->first();
    }

    /**
     * @param array $userIds
     *
     * @return Collection
     */
    public function getNotificationSubscriptionsByUserIds(array $userIds) : Collection
    {
        $notificationSubscriptionTable = $this->getTableName(NotificationSubscription::class);

        return DB::table($notificationSubscriptionTable)
            ->whereIn($notificationSubscriptionTable.'.user_id', $userIds)
            ->get();
    }

    /**
     * @param string $restoreToken
     *
     * @return null
     */
    public function getByRestoreToken(string $restoreToken)
    {
        $passwordReset = (new PasswordReset)->where('token', $restoreToken)->first();

        if ($passwordReset) {
            return $this->findWhere(['email' => $passwordReset->email])->first();
        }

        return null;
    }

    /**
     * @param array $attributes
     *
     * @return User
     */
    public function createUser(array $attributes) : User
    {
        if (!isset($attributes['is_confirmed']) || !$attributes['is_confirmed']) {
            $attributes['confirm_hash'] = $attributes['password'] ?? str_random(32);
        }

        /** @var User $user */
        $user = $this->model->fill($attributes);
        $user->password = $attributes['password'];
        $user->save();

        return $this->find($user->id);
    }

    /**
     * @param array $attributes
     * @param int   $userId
     * @param bool  $timestamps
     *
     * @return bool
     */
    public function updateUser(array $attributes, int $userId, bool $timestamps = true) : bool
    {
        return $this->model->where('id', $userId)->update($attributes, ['timestamps' => $timestamps]);
    }

    /**
     * @param string $password
     * @param int    $userId
     *
     * @return User
     */
    public function changePassword(string $password, int $userId)
    {
        /** @var User $user */
        if ($user = $this->find($userId)) {
            $user->password = $password;
            $user->save();

            return $user;
        }

        return null;
    }

    /**
     * @param array $taskIds
     *
     * @return Collection
     */
    public function getActiveSubscribersByTaskIds(array $taskIds) : Collection
    {
        return DB::table($this->userTable)
            ->select([
                $this->userTable.'.id',
                $this->userTable.'.name',
                $this->userTable.'.last_name',
                $this->userTable.'.nickname',
                $this->userTable.'.email',
                $this->userTable.'.chosen_tenant_id',
                $this->userTable.'.is_confirmed',
                $this->userTable.'.last_activity',
            ])
            ->leftJoin($this->notificationSubscriptionTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->notificationSubscriptionTable.'.user_id', '=', $this->userTable.'.id');
            })
            ->whereIn($this->notificationSubscriptionTable.'.task_id', $taskIds)
            ->get()
            ->unique('id');
    }

    /**
     * @param array $ids
     *
     * @return Collection
     */
    public function getActiveSubscribersByIds(array $ids) : Collection
    {
        return DB::table($this->userTable)
            ->select([
                $this->userTable.'.id',
                $this->userTable.'.name',
                $this->userTable.'.last_name',
                $this->userTable.'.nickname',
                $this->userTable.'.email',
                $this->userTable.'.chosen_tenant_id',
                $this->userTable.'.is_confirmed',
                $this->userTable.'.last_activity',
            ])
            ->leftJoin($this->notificationSubscriptionTable, function ($join) {
                /** @var $join JoinClause */
                $join->on($this->notificationSubscriptionTable.'.user_id', '=', $this->userTable.'.id');
            })
            ->whereIn($this->notificationSubscriptionTable.'.user_id', $ids)
            ->get()
            ->unique('id');
    }
}
