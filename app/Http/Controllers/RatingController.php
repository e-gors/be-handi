<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
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
                    'uuid' => $request->uuid,
                    'commentator_id' => $user->id,
                    'comment' => $request->comment,
                    'rating' => $request->rating,
                ]);
        }
        } catch (Exception $th) {
            $th->getMessage();
       }
    }

    public function getReviews($uuid)
    {
        return Rating::where('uuid', $uuid)->get();
    }
}