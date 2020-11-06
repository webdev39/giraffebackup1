<?php

namespace App\Http\Controllers\Api;

use App\Events\UserTenantHasBeenCreated;
use App\Http\Requests\Auth\JoinRequest;
use App\Http\Requests\ConfirmRequest;
use App\Http\Requests\GetConfirmPasswordRequest;
use App\Http\Requests\GetJoinPasswordRequest;
use App\Http\Requests\GetRestorePasswordRequest;
use App\Http\Requests\InviteRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\RestorePasswordRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserTenantResource;
use App\Http\Responses\AuthResponse;
use App\Jobs\SendConfirmRegistrationEmail;
use App\Jobs\SendResetPasswordEmail;
use App\Models\Permission;
use App\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\UserTenant;
use App\Mail\SendConfirmInviteEmail;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers\Api
 */
class AuthController extends Controller
{
    /**
     * @var AuthService
     */
    private $authService;

    /**
     * AuthController constructor.
     *
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService  = $authService;
    }

    /**
     * @return AuthResponse
     */
    public function authenticate() : AuthResponse
    {
        return new AuthResponse($this->authService->refreshToken());
    }

    /**
     * @param LoginRequest $request
     *
     * @return AuthResponse|JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $user = $this->authService->getUserByEmail($request->get('email'));

        abort_if(!$user, 422,  __('auth.invalid'));
        abort_if(!$user->is_confirmed, 403,'You need to confirm your registration by email');
        abort_if($user->status != 'active', 403, 'You account was deactivated');

        if ($token = $this->authService->login($request->all(['email', 'password']))) {
            return new AuthResponse($token);
        }

        return abort(422, 'Invalid email or password');
    }

    /**
     * @return Response
     */
    public function logout()
    {
        return response($this->authService->logout() ? 200 : 500);
    }

    /**
     * @param RegisterRequest $request
     *
     * @return JsonResponse
     */
    public function register(RegisterRequest $request) : JsonResponse
    {
        if ($user = $this->authService->createUser($request->all(['name', 'last_name', 'email', 'password']))) {
            $this->dispatch(new SendConfirmRegistrationEmail('emails.confirm', $user));

            return response()->json([
                'user' => new UserResource($user),
            ]);
        }

        return abort(500, 'Failed to create new user');
    }

    /**
     * @param GetConfirmPasswordRequest $request
     *
     * @return JsonResponse
     */
    public function confirmHash(GetConfirmPasswordRequest $request) : JsonResponse
    {
        if ($user = $this->authService->getUserByConfirmHash($request->get('confirm_hash'))) {
            return response()->json([
                'user' => new UserResource($user),
            ]);
        }

        return abort(404, 'User is not registered or already confirmed the account');
    }

    /**
     * @param ConfirmRequest $request
     *
     * @return AuthResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Throwable
     */
    public function confirm(ConfirmRequest $request)
    {
        $userTenant = $this->authService->createTenant($request->get('type'), $request->get('name'), $request->get('user_id'));
        app('TenantSer')->markTenantAsChosen($userTenant->user_id, $userTenant->tenant_id);

        event(new UserTenantHasBeenCreated($userTenant));

        $user = $this->authService->markUserAsConfirmed($request->get('user_id'));
        $this->authService->attachToUserTenantRole($userTenant, Role::whereName(Role::OWNER_ROLE['name'])->pluck('id'), true);

        if ($userTenant && $user) {
            return new AuthResponse($this->authService->loginFromUser($user), $user);
        }

        return abort(500, 'Failed to create new tenant');
    }

    /**
     * @param GetRestorePasswordRequest $request
     *
     * @return JsonResponse
     */
    public function getRestore(GetRestorePasswordRequest $request)
    {
        if ($user = app('UserRepo')->getByRestoreToken($request->get('resetToken'))) {
            return response()->json([
                'user' => new UserResource($user),
            ]);
        }

        return abort(404, 'The link is expired');
    }

    /**
     * @param RestorePasswordRequest $request
     *
     * @return AuthResponse|JsonResponse
     * @throws \Throwable
     */
    public function restore(RestorePasswordRequest $request)
    {
        $resetToken = $request->get('token');
        $password   = $request->get('password');
        $user       = app('UserRepo')->getByRestoreToken($resetToken);

        if ($user) {
            $result = DB::transaction(function () use ($user, $password, $resetToken) {
                $this->authService->updateUserPassword($password, $user->id);

                return $this->authService->removeResetToken($resetToken);
            });

            if ($result) {
                return new AuthResponse($this->authService->loginFromUser($user), $user);
            }

            return abort(500, 'Password in not changed');
        }

        return abort(404, 'You already restored the password by this link');
    }

    /**
     * Method creates reset password token for user, sends restore password link on email
     *
     * @param ResetPasswordRequest $request
     *
     * @return JsonResponse
     */
    public function reset(ResetPasswordRequest $request)
    {
        try {
            $user = $this->authService->getUserByEmail($request->get('email'), true);

            if (!$user) {
                abort(404, 'This user doesn\'t exist');
            }

            $token = $this->authService->createResetToken($user);
            $this->dispatch(new SendResetPasswordEmail('emails.reset_password', $user, $token));

            return response()->json(['message' => 'Success']);
        } catch (\Exception $ex) {
            abort(500, $ex->getMessage());
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvite()
    {
        Auth::failIfHasNoPermission(Permission::CAN_INVITE_OTHERS_PERMISSIONS);

        if ($tenants = Auth::user()->tenants) {
            return response()->json([
                'tenants' => $tenants
            ]);
        }

        return abort(404, 'User has no tenants! Create it before invite users');
    }

    /**
     * @param InviteRequest $request
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function invite(InviteRequest $request)
    {
        /** @var UserTenant $userTenant */
        $userTenant = Auth::userTenant();

        Auth::failIfHasNoPermission(Permission::CAN_INVITE_OTHERS_PERMISSIONS);

        if ($this->authService->getUserByEmail($request->get('email'))) {
            return abort(302, 'This email is already in use by another user');
        }

        $attributes = $request->all(['name', 'last_name', 'email', 'group_roles', 'company_name', 'roles']);
        $attributes['password'] = $request->get('without_verify') ? str_random(8) : null;

        try {
            $canInviteOthers = false;
            $canInviteOthersRole = Role::whereName(Permission::CAN_INVITE_OTHERS_PERMISSIONS['name'])->first();
            if(in_array($canInviteOthersRole->id, $attributes['roles'])) {
                $attributes['can_invited'] = true;
                $canInviteOthers = true;
            }

            $userTenant = $this->authService->inviteUser($attributes, $userTenant, $canInviteOthers);

            Mail::to($userTenant->user)->queue(new SendConfirmInviteEmail($userTenant, $attributes['password']));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

        $userTenant = app('TenantSer')->getUserTenantWithRelationsById($userTenant->id);

        return response()->json([
            'member' => new UserTenantResource($userTenant)
        ]);
    }

    /**
     * @param GetJoinPasswordRequest $request
     *
     * @return AuthResponse|JsonResponse
     */
    public function getJoin(GetJoinPasswordRequest $request)
    {
        try {
            /** @var UserTenant $userTenant */
            $userTenant = $this->authService->getUserTenantByInviteHash($request->get('invite_hash'));

            if ($userTenant) {
                $user = $userTenant->user;

                if (!empty($user->password)) {
                    $userTenant = $this->authService->confirmTenantInviteByHash($request->get('invite_hash'));

                    if (!$userTenant) {
                        abort(422, 'User is already joined');
                    }

                    app('TenantSer')->markTenantAsChosen($user->id, $userTenant->tenant_id);

                    $user = $this->authService->markUserAsConfirmed($user->id);

                    if (!$user) {
                        abort(500, 'Failed to confirm new member');
                    }

                    return new AuthResponse($this->authService->loginFromUser($user), $user);
                }

                return response()->json([
                    'user' => new UserResource($user),
                ]);
            }
        } catch (\Exception $ex) {
            dd($ex);
            abort(500, $ex->getMessage());
        }

        return response()->json(['message' => 'User is not registered or already confirmed the inviting'], 403);
    }

    /**
     * @param JoinRequest $request
     *
     * @return AuthResponse|JsonResponse
     */
    public function join(JoinRequest $request)
    {
        try {
            /** @var UserTenant $userTenant */
            $userTenant = $this->authService->confirmTenantInviteByHash($request->get('invite_hash'));

            if (!$userTenant) {
                abort(422, 'User is already joined');
            }

            $user = $this->authService->updateUserPassword($request->get('password'), $request->get('user_id'));

            if (!$user) {
                abort(404, 'User is not confirmed');
            }

            app('TenantSer')->markTenantAsChosen($request->get('user_id'), $userTenant->tenant_id);

            if ($user = $this->authService->markUserAsConfirmed($request->get('user_id'))) {
                return new AuthResponse($this->authService->loginFromUser($user), $user);
            }

        } catch (\Exception $ex) {
            abort(500, $ex->getMessage());
        }

        abort(500, 'Failed to confirm new member');
    }

}
