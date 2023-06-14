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

            // Check if the user and worker have a completed contract
            $query = Contract::query();
            $query->where('status', 'completed');

            $query->with(['post.user', 'bid.user', 'offer.user']);

            $query->where(function ($query) use ($user, $worker) {
                $query->whereHas('post', function ($query) use ($user, $worker) {
                    $query->where('user_id', $user->id)
                        ->whereHas('bids', function ($query) use ($worker) {
                            $query->where('user_id', $worker->id);
                        });
                });
            })
                ->orWhereHas('offer', function ($query) use ($user, $worker) {
                    $query->where('profile_id', $worker->id)
                        ->where('user_id', $user->id);
                });

            if (!$query->exists()) {
                return response()->json([
                    'code' => 400,
                    'message' => 'You cannot submit a review without a completed contract.'
                ]);
            }

            Rating::create([
                'user_id' => $user->id,
                'worker_id' => $worker->id,
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
