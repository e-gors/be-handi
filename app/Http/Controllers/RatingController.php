<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'comment' => 'required',
            'rating' => 'required|numeric|min:1|max:5',
            'reviewable_id' => 'required',
            'reviewable_type' => 'required|in:User,Post',
        ]);

        $user = auth()->user();
        $reviewable = $validatedData['reviewable_type']::findOrFail($validatedData['reviewable_id']);

        $review = $user->reviews->create([
            'comment' => $validatedData['comment'],
            'rating' => $validatedData['rating'],
            'reviewable_id' => $reviewable->id,
            'reviewable_type' => get_class($reviewable),
        ]);

        return response()->json($review, 201);
    }
}
