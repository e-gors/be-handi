<?php

namespace App\Http\Controllers;

use App\User;
use App\Tracker;
use Illuminate\Http\Request;
use App\Http\Resources\WorkerResource;

class TrackerController extends Controller
{
    public function profileView($uuid)
    {
        $user = User::where('uuid', $uuid)->first();

        $tracker = Tracker::where('user_id', $user->id)->first();

        if ($tracker) {
            $tracker->increment('profile_view');
        } else {
            Tracker::create([
                'user_id' => $user->id,
                'profile_view' => 1
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Incremented successfully!',
            'worker' => new WorkerResource($user)
        ]);
    }
}
