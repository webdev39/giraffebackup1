<?php

namespace App\Services\User;

use App\Models\User;
use App\Models\UserColorScheme;
use App\Repositories\UserRepositoryEloquent;
use App\Services\BaseService;
use Illuminate\Support\Collection;

class UserService extends BaseService
{
    /** @var UserRepositoryEloquent  */
    private $userRepo;

    /**
     * UserService constructor.
     */
    public function __construct()
    {
        $this->userRepo = app('UserRepo');
    }

    /**
     * @param array $userIds
     *
     * @return Collection
     */
    public function getUserByIds(array $userIds) : Collection
    {
        return $this->userRepo->findWhereIn('id', $userIds);
    }

    /**
     * @param $nickname
     * @param $user
     *
     * @return string
     */
    public static function getUserNickname($nickname, $user) : string
    {
        return empty($nickname) ? strtolower("{$user->name}_{$user->last_name}") : $nickname;;
    }

    public static function getColorSchema(int $userId): UserColorScheme
    {
        return  User::find($userId)->colorSchema();
    }
}