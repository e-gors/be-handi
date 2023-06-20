<?php

namespace App\Http\Controllers;

use App\Bid;
use App\Post;
use App\Offer;
use Exception;
use App\Contract;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OfferResource;
use App\Http\Resources\ClientResource;
use App\Http\Resources\WorkerResource;
use Illuminate\Support\Facades\Storage;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search ? $request->search : null;
        $status = $request->status ? $request->status : null;
        $type = $request->type ? $request->type : null;

        $user = auth()->user();
        $query = Offer::query();

        $query->with(['user', 'post']);

        if (!is_null($status)) {
            $query->where('status', $status);
        }
        if (!is_null($type)) {
            $query->whereHas('post', function ($query) use ($type) {
                $query->where('job_type', $type);
            });
        }

        if ($user->role === 'Client') {
            $query->where('user_id', $user->id);

            if (!is_null($search)) {
                $query->join('users', 'offers.profile_id', '=', 'users.id')
                    ->where(function ($query) use ($search) {
                        $query->where('users.first_name', 'LIKE', "%$search%")
                            ->orWhere('users.last_name', 'LIKE', "%$search%")
                            ->orWhere(DB::raw("CONCAT(users.first_name, ' ', users.last_name)"), 'LIKE', "%$search%");
                    })
                    ->orWhereHas('post', function ($query) use ($search) {
                        $query->where('title', 'LIKE', "%$search%")
                            ->orWhere('position', 'LIKE', "%$search%");
                    });
            }
        } else {
            $query->where('profile_id', $user->id);

            if (!is_null($search)) {
                $query->join('users', 'offers.user_id', '=', 'users.id')
                    ->where(function ($query) use ($search) {
                        $query->where('users.first_name', 'LIKE', "%$search%")
                            ->orWhere('users.last_name', 'LIKE', "%$search%")
                            ->orWhere(DB::raw("CONCAT(users.first_name, ' ', users.last_name)"), 'LIKE', "%$search%");
                    })
                    ->orWhereHas('post', function ($query) use ($search) {
                        $query->where('title', 'LIKE', "%$search%")
                            ->orWhere('position', 'LIKE', "%$search%");
                    });
            }
        }

        return OfferResource::collection($this->paginated($query, $request));
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $worker = $request->worker ? json_decode($request->worker) : null;
            $job = $request->job ? json_decode($request->job) : null;

            if (empty($worker)) {
                return response()->json([
                    'code' => 500,
                    'message' => "Contractor field is required!"
                ]);
            }

            $hasOffer = Offer::where('user_id', $user->id)
                ->where('profile_id', $worker[0]->id)
                ->where('post_id', $job[0]->id)->first();

            if ($hasOffer) {
                return response()->json([
                    'code' => 500,
                    'message' => "You already have sent offer to this user with the job you choose!"
                ]);
            }
            $newOffer = Offer::create([
                'user_id' => $user->id,
                'profile_id' => $worker[0]->id,
                'post_id' => $job[0]->id,
            ]);

            if (!$newOffer) {
                return response()->json([
                    'code' => 500,
                    'message' => "Encounter Error while saving your offer!"
                ]);
            } else {
                $this->sendNewOfferNotification($worker, $newOffer, $user);

                return response()->json([
                    'code' => 200,
                    'message' => "Successfully send new offer to User"
                ]);
            }
        } catch (Exception $e) {
            return $e;
        }
    }

    public function accept(Offer $offer)
    {
        $user = auth()->user();
        $post = Post::find($offer->post_id);
        $bid = $post ? Bid::find($post->bid_id) : null;

        if ($post->status === 'contracted') {
            return response()->json([
                'code' => 500,
                'message' => "I'm sorry but this offer is already in contract mode!"
            ]);

            $offer->update([
                'status' => "Expired"
            ]);
        }
        $days = "21-30 days";
        $startDay = Carbon::parse('next monday')->startOfWeek(); // Start date as next Monday
        $dayRange = explode('-', $days);
        $pattern = '/(\d+)/'; // Regular expression pattern to match digits

        preg_match($pattern, $dayRange[1], $matches);
        $endDay = $startDay->copy()->addDays($matches[0])->endOfWeek(); // End date as start date + number of days

        $startDateString = $startDay->toDateString();
        $endDateString = $endDay->toDateString();

        $newContracts = Contract::create([
            'post_id' => $post ? $post->id : null,
            'bid_id' => $bid ? $bid->id : null,
            'offer_id' => $offer->id,
            'start_date' => $startDateString,
            'end_date' => $endDateString,
            'status' => 'in progress'
        ]);

        $offer->update([
            'status' => 'accepted'
        ]);

        $post->update([
            'status' => 'contracted'
        ]);

        if ($newContracts) {
            return response()->json([
                'code' => 200,
                'message' => "You accepted an offer",
                'user' => new WorkerResource($user),
            ]);
        }
    }

    public function cancel(Offer $offer)
    {
        $user = auth()->user();
        if ($offer) {
            $offer->update([
                'status' => 'declined'
            ]);

            return response()->json([
                'code' => 200,
                'message' => 'You successfully decline offer!',
                'user' => new WorkerResource($user)
            ]);
        } else {
            return response()->json([
                'code' => 404,
                'message' => 'Offer not found!',
            ]);
        }
    }
    public function withdraw(Offer $offer)
    {
        $user = auth()->user();
        if ($offer) {
            $offer->update([
                'status' => 'withdrawn'
            ]);

            return response()->json([
                'code' => 200,
                'message' => 'You successfully withdraw offer!',
                'user' => $user->role === "Client" ? new ClientResource($user) : new WorkerResource($user)
            ]);
        } else {
            return response()->json([
                'code' => 404,
                'message' => 'Offer not found!',
            ]);
        }
    }
}
