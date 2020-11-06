<?php

namespace App\Services\UserProfile;

use App\Collections\UserCollection;
use App\Models\Language;
use App\Models\User;
use App\Repositories\UserProfileRepositoryEloquent;
use App\Repositories\UserRepositoryEloquent;
use App\Services\BaseService;

/**
 * Class AuthService
 *
 * @package App\Services
 */
class UserProfileService extends BaseService
{
    /** @var UserRepositoryEloquent */
    private $userRepo;

    /** @var UserProfileRepositoryEloquent */
    private $userProfileRepo;

    /**
     * UserProfileService constructor.
     */
    public function __construct()
    {
        $this->userRepo         = app('UserRepo');
        $this->userProfileRepo  = app('UserProfileRepo');
    }

    /**
     * @param string $password
     * @param User   $user
     *
     * @return User
     */
    public function updatePassword(string $password, User $user) : User
    {
        $user->password = $password;
        $user->save();

        return $user;
    }

    /**
     * @param array $attributes
     * @param User  $user
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateUser(array $attributes, User $user)
    {
        if ($attributes['nickname'] == $user->nickname) {
            unset($attributes['nickname']);
        }

        if (isset($attributes['avatar'])) {
            $avatar_id = $attributes['avatar'] == 'remove' ? null : app('ImageSer')->uploadImage($attributes['avatar'], $user->id);

            $this->userProfileRepo->update(['avatar_id' => $avatar_id], $user->userProfile->id);
        }

        return $this->userRepo->update($attributes, $user->id);
    }

    /**
     * @param array $attributes
     * @param User  $user
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateUserProfile(array $attributes, User $user)
    {
        if (!$userProfile = $user->userProfile) {
            $user->userProfile()->create();
        }

        if (isset($attributes['background'])) {
            $attributes['background_id'] = $attributes['background'] == 'remove' ? null : app('ImageSer')->uploadImage($attributes['background'], $user->id);
        }

        $this->userRepo->sync($user->id, 'notificationType', $attributes['notification_types'] ?? []);

        return $this->userProfileRepo->update($attributes, $userProfile->id);
    }

    /**
     * @param int $userId
     *
     * @return mixed
     */
    public function getUserById(int $userId)
    {
        return $this->userRepo->find($userId);
    }

    /**
     * @param int $userId
     *
     * @return mixed
     */
    public function getUserProfileWithRelationsByUserId(int $userId)
    {
        $user               = app('UserRepo')->getUserById($userId);
        $user->notify_types = User::createFromStd($user)->notificationType;
        $user->role         = app('RoleRepo')->getRolesByUserTenantIds([$user->user_tenant_id])->first();

        if (!$user->font_id) {
            $user->font_id = app('FieldRepo')->getDefaultFount()->id;
        }

        if (!$user->language_id) {
            $user->language_id = optional(Language::getDefaultLanguage())->id;
        }

        return $user;
    }
}
