<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\NotificationCollectionResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Notifications\TestFcmNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * @return NotificationCollectionResource
     */
    public function index() : NotificationCollectionResource
    {
        $notificationsQuery = Auth::user()->notifications();

        /** @var \Notification[] $notifications */
        $notifications = $notificationsQuery->paginate(30);

        return new NotificationCollectionResource($notifications);
    }

    /**
     * @param string $notifyId
     * @param string $status
     *
     * @return JsonResponse
     */
    public function updateStatus(string $notifyId, string $status)
    {
        app('NotificationSer')->changeStatusNotification(Auth::user(), $notifyId, $status);

        return response()->json(['message' => 'Success']);
    }

    /**
     * @return JsonResponse
     */
    public function allMarkRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'Success']);
    }

    public function addDevice(Request $request)
    {
        if (! $request->token) {
            abort(422);
        }
        $user = auth()->user();
        $user->fcmTokens()->save([
            'token' => $request->token,
        ]);
        $user->notify(new TestFcmNotification());
        return response()->json(['status' => 'OK']);
    }

    public function unread(Request $request) {
        return response()->json([
            'notifications' => NotificationResource::collection(auth()->user()->unreadNotifications)
        ]);
    }

}
