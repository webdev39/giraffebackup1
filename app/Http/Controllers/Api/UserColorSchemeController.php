<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserColorSchemeRequest;
use App\Http\Resources\UserColorSchemeResource;
use App\Models\UserColorScheme;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class UserColorSchemeController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param UserColorSchemeRequest $request
     * @return JsonResponse
     */
    public function store(UserColorSchemeRequest $request)
    {
        $userColorScheme = \Auth::user()->colorSchemes()->create($request->only(['sidebar', 'navbar', 'task_detail', 'manage', 'subscribers', 'management', 'modal', 'buttons', 'font', 'notification']));

        return response()->json(new UserColorSchemeResource($userColorScheme));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserColorSchemeRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UserColorSchemeRequest $request, int $id)
    {
        $userColorScheme = UserColorScheme::findOrFail($id);

        $this->authorize('update', $userColorScheme);

        $userColorScheme->update($request->only(['sidebar', 'navbar', 'task_detail', 'manage', 'subscribers', 'management', 'modal', 'buttons']));

        return response()->json(new UserColorSchemeResource($userColorScheme));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(UserColorScheme $userColorScheme, int $id)
    {
        $userColorScheme = UserColorScheme::findOrFail($id);

        $this->authorize('destroy', $userColorScheme);
        $userColorScheme->delete();

        return response()->json(['message' => 'Success']);
    }
}
