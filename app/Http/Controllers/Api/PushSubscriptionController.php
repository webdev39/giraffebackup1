<?php

namespace App\Http\Controllers\Api;

use App\Notifications\Fcm\HelloNotification as HelloNotificationFcm;
use App\Notifications\HelloNotification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class PushSubscriptionController extends Controller
{
    use ValidatesRequests;

    /**
     * Update user's subscription.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, ['endpoint' => 'required']);

        $request->user()->updatePushSubscription(
            $request->endpoint,
            $request->key,
            $request->token
        );


        return response()->json('Notification sent.', 201);
    }

    public function sendWelcome(Request $request)
    {
        $request->user()->notify(new HelloNotification);
        try {
            Notification::send($request->user()->devicesTokens, new HelloNotificationFcm);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), [$exception->getFile(), $exception->getLine(), $exception->getCode()]);
        }
    }

    /**
     * Delete the specified subscription.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $this->validate($request, ['endpoint' => 'required']);

        $request->user()->deletePushSubscription($request->endpoint);

        return response()->json(null, 204);
    }
}
