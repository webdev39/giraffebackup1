<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeUserPasswordRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserProfileResource;
use App\Services\UserProfile\UserProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class UserProfileController
 *
 * @package App\Http\Controllers\Api
 */
class UserProfileController extends Controller
{
    /** @var UserProfileService */
    private $userProfileService;

    /**
     * UserProfileController constructor.
     */
    public function __construct()
    {
        $this->userProfileService = app('UserProfileSer');
    }

    /**
     * @return JsonResponse
     */
    public function show() : JsonResponse
    {
        $userProfile = $this->userProfileService->getUserProfileWithRelationsByUserId(Auth::id());

        return response()->json([
            'profile' => new UserProfileResource($userProfile)
        ]);
    }

    /**
     * @param UpdateUserRequest $request
     *
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function updateUser(UpdateUserRequest $request) : JsonResponse
    {
        $user = $this->userProfileService->updateUser($request->all(), Auth::user());

        if (!$user) {
            abort(404, 'User is not found');
        }

        $userProfile = $this->userProfileService->getUserProfileWithRelationsByUserId(Auth::id());

        return response()->json([
            'profile' => new UserProfileResource($userProfile)
        ]);
    }

    /**
     * @param UpdateUserProfileRequest $request
     *
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function updateProfile(UpdateUserProfileRequest $request) : JsonResponse
    {
        $profile = $this->userProfileService->updateUserProfile($request->all(), Auth::user());

        if (!$profile) {
            abort(404, 'User profile is not found');
        }

        $userProfile = $this->userProfileService->getUserProfileWithRelationsByUserId(Auth::id());

        return response()->json([
            'profile' => new UserProfileResource($userProfile)
        ]);
    }

    /**
     * @param ChangeUserPasswordRequest $request
     *
     * @return JsonResponse
     */
    public function changePassword(ChangeUserPasswordRequest $request)
    {
        if (!Hash::check($request->get('old_password'), Auth::user()->password)) {
            abort(422, 'Wrong old password');
        }

        $this->userProfileService->updatePassword($request->get('password'), Auth::user());

        return response()->json([
            'message' => 'Success'
        ]);
    }
}