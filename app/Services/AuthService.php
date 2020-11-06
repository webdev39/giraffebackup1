<?php

namespace App\Services;

use App\Models\Group;
use App\Models\PasswordReset;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserTenant;
use App\Repositories\RoleRepositoryEloquent;
use App\Repositories\TenantRepositoryEloquent;
use App\Repositories\UserRepositoryEloquent;
use App\Repositories\UserTenantRepositoryEloquent;
use App\Services\Group\GroupService;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\JWTAuth;

/**
 * Class AuthService
 *
 * @package App\Services
 */
class AuthService extends BaseService
{
    /** @var JWTAuth */
    private $jwtAuth;

    /** @var TenantRepositoryEloquent */
    private $tenantRepo;

    /** @var UserRepositoryEloquent */
    private $userRepo;

    /** @var UserTenantRepositoryEloquent */
    private $userTenantRepo;
    /**
     * @var GroupService
     */
    private $groupService;
    /**
     * @var RoleRepositoryEloquent
     */
    private $roleRepositoryEloquent;

    /**
     * AuthService constructor.
     * @param JWTAuth $jwtAuth
     */
    public function __construct(JWTAuth $jwtAuth)
    {
        $this->jwtAuth          = $jwtAuth;

        $this->tenantRepo       = app('TenantRepo');
        $this->userTenantRepo   = app('UserTenantRepo');
        $this->userRepo         = app('UserRepo');
        $this->groupService = app('GroupSer');
        $this->roleRepositoryEloquent = app('RoleRepo');
    }

    /**
     * @param array $credentials
     *
     * @return false|string
     */
    public function login(array $credentials = [])
    {
        try {
            return $this->jwtAuth->attempt(array_merge($credentials, ['is_confirmed' => true]));
        } catch (\Exception $e) {
            abort(500, 'These credentials do not match our records.');
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return null|string
     */
    public function loginFromUser(User $user)
    {
        try {
            return $this->jwtAuth->fromUser($user);
        } catch (\Exception $e) {
            abort(500, 'These credentials do not match our records.');
        }

        return null;
    }

    /**
     * @return bool
     */
    public function logout()
    {
        try {
            return $this->jwtAuth->invalidate($this->jwtAuth->getToken());
        } catch (\Exception $ex) {
            abort(500, $ex->getMessage());
        }

        return false;
    }

    /**
     * @return null|string
     */
    public function refreshToken()
    {
        try {
            $token = $this->loginFromUser($this->jwtAuth->toUser());
//            $this->jwtAuth->invalidate($this->jwtAuth->getToken());

            return $token;
        } catch (\Exception $ex) {
            abort(500, $ex->getMessage());
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
        /** @var User $user */
        $user = $this->userRepo->createUser($attributes);
        $user->userProfile()->create();

        app('NotificationSer')->enableAllNotifications($user);

        return $user;
    }

    /**
     * @param string $confirmHash
     *
     * @return mixed
     */
    public function getUserByConfirmHash(string $confirmHash)
    {
        return $this->userRepo->getUserByConfirmHash($confirmHash);
    }

    /**
     * @param array $attributes
     *
     * @return mixed|null
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createUserTenant(array $attributes)
    {
        if ($tenant = $this->tenantRepo->find($attributes['tenant_id'])) {
            return $this->userTenantRepo->create($attributes);
        }

        return null;
    }

    /**
     * @param string $email
     * @param bool   $isConfirmed
     *
     * @return mixed
     */
    public function getUserByEmail(string $email, bool $isConfirmed = null)
    {
        if (is_bool($isConfirmed)) {
            return $this->userRepo->findWhere(['email' => $email, 'is_confirmed' => $isConfirmed])->first();
        }

        return $this->userRepo->findWhere(['email' => $email])->first();
    }

    /**
     * @param string $password
     * @param int    $userId
     *
     * @return mixed
     */
    public function changeUserPassword(string $password, int $userId)
    {
        return $this->userRepo->changePassword($password, $userId);
    }

    /**
     * @param string $firstName
     * @param string $lastName
     *
     * @return mixed
     */
    public function checkUserName(string $firstName, string $lastName)
    {
        return $this->userRepo->findWhere(['name' => $firstName, 'last_name' => $lastName])->first();
    }

    /**
     * @param int $userId
     *
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function markUserAsConfirmed(int $userId)
    {
        return $this->userRepo->update(['is_confirmed' => 1, 'confirm_hash' => null], $userId);
    }

    /**
     * @param string $password
     * @param int    $userId
     *
     * @return User|null
     */
    public function updateUserPassword(string $password, int $userId)
    {
        /** @var User $user */
        if ($user = User::find($userId)) {
            $user->password = $password;
            $user->save();

            return $user;
        }

        return null;
    }

    /**
     * @param string $inviteHash
     *
     * @return mixed
     */
    public function getUserTenantByInviteHash(string $inviteHash)
    {
        return $this->userTenantRepo->findWhere(['invite_hash' => $inviteHash])->first();
    }

    /**
     * @param UserTenant $userTenant
     * @param $rolesIds
     * @param bool $canInvited
     * @return array
     * @throws \ReflectionException
     */
    public function attachToUserTenantRole(UserTenant $userTenant, $rolesIds, bool $canInvited = false)
    {
        $roles = [];
        if(is_array($rolesIds) || $rolesIds instanceof \ArrayAccess) {
            foreach ($rolesIds as $roleId) {
                $role = Role::where('id', $roleId)->firstOrFail();

                if (!$role) {
                    abort(404, 'Role #'.$roleId.' not found');
                }

                $roles[$role->id] = ['can_invited' => $canInvited];
            }
        } else {
            $oClass = new \ReflectionClass(Role::class);
            $constants = $oClass->getConstants();
            foreach ($constants as $constant) {
                if(is_array($constant) && !empty($constant['name']) && $constant['name'] == $rolesIds) {
                    $roles = Role::whereIn('name', collect($constant['permissions'])->pluck('name'))
                        ->pluck('name', 'id')
                        ->map(function($roleName) use($canInvited) {
                            return ['can_invited' => $canInvited];
                        });
                }
            }
        }


        return $userTenant->roles()->sync($roles);
    }

    /**
     * @param array      $attributes
     * @param UserTenant $userTenant
     * @param bool       $canInvited
     *
     * @return mixed
     * @throws \Throwable
     */
    public function inviteUser(array $attributes, UserTenant $userTenant, bool $canInvited = false)
    {
        $attributes['password'] = isset($attributes['password']) ? $attributes['password'] : null;

        return DB::transaction(function () use ($attributes, $userTenant, $canInvited) {
            $user = $this->createUser(array_merge($attributes, [
                'chosen_tenant_id'  => $userTenant->tenant_id,
                'password'          => $attributes['password'],
                'is_confirmed'      => !is_null($attributes['password']),
                'can_invited'       => $canInvited,
            ]));

            if (!$user) {
                throw new \Exception('Failed to add a member');
            }

            $userTenant = $this->createUserTenant([
                'invite_hash'   => is_null($attributes['password']),
                'company_name'  => $attributes['company_name'] ?? null,
                'tenant_id'     => $userTenant->tenant_id,
                'user_id'       => $user->id,
                'is_owner'      => false,
                'can_invited'   => $canInvited,
            ]);

            if (!$userTenant) {
                throw new \Exception('There is no tenant id user choose');
            }

            if(!empty($attributes['roles']) && is_array($attributes['roles'])) {
                $this->attachToUserTenantRole($userTenant, $attributes['roles'], $canInvited);
            }

            $this->groupService->attachUserToGroupWithRoles($attributes['group_roles'], $userTenant);

            return $userTenant;
        });
    }

    /**
     * @param string $type
     * @param string $name
     * @param int    $userId
     *
     * @return mixed
     * @throws \Throwable
     */
    public function createTenant(string $type, string $name, int $userId)
    {
        /** @var User $user */
        $user = app('UserRepo')->find($userId);

        return DB::transaction(function () use ($type, $name, $user) {
            /** @var Tenant $tenant */
            $tenant = app('TenantRepo')->create([$type => $name]);

            $userTenant = $this->createUserTenant([
                'user_id'       => $user->id,
                'tenant_id'     => $tenant->id,
                'is_owner'      => true,
            ]);

            app('SubscriptionRepo')->createTenantSubscription($tenant);

            return $userTenant;
        });
    }

    /**
     * @param User $user
     *
     * @return string
     */
    public function createResetToken(User $user) : string
    {
        return $user->createPasswordResetToken();
    }

    /**
     * @param string $inviteHash
     *
     * @return UserTenant|null
     */
    public function confirmTenantInviteByHash(string $inviteHash)
    {
        /** @var UserTenant $userTenant */
        $userTenant = app('UserTenantRepo')->findByField('invite_hash', $inviteHash)->first();

        if ($userTenant) {
            $this->confirmTenantInvite($userTenant);

            return $userTenant;
        }

        return null;
    }

    /**
     * @param int $userId
     *
     * @return bool|null
     */
    public function confirmTenantInviteByUserId(int $userId)
    {
        /** @var UserTenant $userTenant */
        $userTenant = app('UserTenantRepo')->find($userId)->first();

        if (!$userTenant) {
            return null;
        }

        $this->confirmTenantInvite($userTenant);
    }

    /**
     * @param UserTenant $userTenant
     *
     * @return bool
     */
    public function confirmTenantInvite(UserTenant $userTenant)
    {
        return $userTenant->update(['invite_hash' => null]);
    }


    /**
     * @param string $resetToken
     *
     * @return bool
     * @throws \Exception
     */
    public function removeResetToken(string $resetToken) : bool
    {
        return PasswordReset::where('token', $resetToken)->delete();
    }
}
