<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pusher\Pusher;

class AuthController extends Controller
{
    public function authenticatePusher(Request $request)
    {
        $socketId = $request->input('socket_id');
        $channelName = $request->input('channel_name');

        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            [
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'useTLS' => true,
            ]
        );

        $user = auth()->user(); // Get the authenticated user
        $presenceData = ['user_id' => $user->id, 'user_info' => ['name' => $user->name]]; // Adjust this data as needed

        $auth = $pusher->authorizePresenceChannel($channelName, $socketId, $user->id, $presenceData);

        return response($auth);
    }
}
