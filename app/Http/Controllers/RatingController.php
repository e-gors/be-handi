<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use App\Rating;
use App\Contract;
use Illuminate\Http\Request;
use App\Http\Resources\WorkerResource;

class RatingController extends Controller
{
    public function store(Request $request, User $worker)
    {
        try {

            $user = auth()->user();
            $job = $request->contract;

            $rated = Rating::where('user_id', $user->id)
                ->where('worker_id', $worker->id)
                ->where('post_id', $job['post']['id'])
                ->exists();

            if ($rated) {
                return response()->json([
                    'code' => 500,
                    'message' => 'You already submit a rating for this contract!'
                ]);
            }
            // Check if the user and worker have a completed contract
            if ($job['status'] !== 'completed') {
                return response()->json([
                    'code' => 400,
                    'message' => 'You cannot submit a review without a completed contract.'
                ]);
            }

            Rating::firstOrCreate([
                'user_id' => $user->id,
                'post_id' => $job['post']['id'],
                'worker_id' => $worker->id,
            ], [
                'comment' => $request->comment,
                'rating' => $request->rating,
            ]);

            return response()->json([
                'code' => 200,
                'message' => 'Review submitted successfully!',
                'worker' => new WorkerResource($worker)
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getReviews(User $client)
    {
        return Rating::where('user_id', $client->id)->get();
    }

    public function updateReview(Request $request, Rating $rating)
    {
        $worker = User::find($rating->worker_id);

        if ($rating) {
            $rating->update([
                'comment' => $request->comment,
                'rating' => $request->rating,
            ]);

            return response()->json([
                'code' => 200,
                'message' => 'Successfully updated review!',
                'worker' => new WorkerResource($worker)
            ]);
        } else {
            return response()->json([
                'code' => 404,
                'message' => "No reviews has been found!"
            ]);
        }
    }

    public function destroy(Rating $rating)
    {
        $worker = User::find($rating->worker_id);

        if ($rating) {
            $rating->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Successfully removed review!',
                'worker' => new WorkerResource($worker)
            ]);
        } else {
            return response()->json([
                'code' => 404,
                'message' => "No reviews has been found!"
            ]);
        }
    }
}
