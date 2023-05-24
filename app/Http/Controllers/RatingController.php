<?php

namespace App\Http\Controllers;
use App\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        $user = auth()->user();
        
       try {
        if ($validatedData) {
            Rating::create([
                'user_id' => $request->user_id,
                'commentator_id' => $request->$user->id,
                'comment' => $request->comment,
                'rating' => $request->rating,
            ]);
        }
       } catch (\Throwable $th) {
        throw $th;
       }
    }
}