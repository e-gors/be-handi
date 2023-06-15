<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function contact(Request $request)
    {
        $this->sendEmailOnContact($request);

        return response()->json([
            'code' => 200,
            'Your suggestion is successfully sent!'
        ]);
    }
}
