<?php

namespace App\Http\Controllers;

use App\Events\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function message(Request $request)
    {
        event(new Message($request->username, $request->message));

        return response()->json([
            'code' => 200,
            'message' => 'Send message successfully!',
        ]);
    }
}
