<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDeviceTokenRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class DeviceTokenController extends Controller
{
    public function store(CreateDeviceTokenRequest $request)
    {
        \Illuminate\Support\Facades\Log::info('New hit the endpoint');
        \Illuminate\Support\Facades\Log::info(json_encode($request->all()));
        Auth::user()->devicesTokens()->firstOrCreate([
            'token' => $request->get('token')
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function destroy(string $token)
    {
        Auth::user()->devicesTokens()->where('token', $token)
            ->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
