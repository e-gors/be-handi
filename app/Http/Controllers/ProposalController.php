<?php

namespace App\Http\Controllers;

use App\Bid;
use App\Post;
use App\User;
use Exception;
use App\Contract;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\BidResource;
use App\Http\Resources\WorkerResource;
use Illuminate\Support\Facades\Storage;

class ProposalController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search ? $request->search : null;
        $orderByRate = $request->order_by_rate ? $request->order_by_rate : null;
        $orderByDate = $request->order_by_date ? $request->order_by_date : null;
        $status = $request->status ? $request->status : null;

        $query = Bid::query();
        $query->with('user', 'post');

        if (!is_null($search)) {
            $query->whereHas('post', function ($q) use ($search) {
                $q->where('title', 'LIKE', "%$search%")
                    ->orWhere('category', 'LIKE', "%$search%")
                    ->orWhere('position', 'LIKE', "%$search%");
            });
        }
        if (!is_null($orderByRate)) {
            $query->orderBy('rate', $orderByRate);
        }
        if (!is_null($orderByDate)) {
            $query->orderBy('created_at', $orderByDate);
        }
        if (!is_null($status)) {
            $query->where('status', $status);
        }
        $query->get();

        return BidResource::collection($this->paginated($query, $request));
    }
    public function userBids(Request $request)
    {

        $search = $request->search ? $request->search : null;
        $orderByRate = $request->order_by_rate ? $request->order_by_rate : null;
        $orderByDate = $request->order_by_date ? $request->order_by_date : null;
        $status = $request->status ? $request->status : null;

        $user = auth()->user();

        $query = Bid::query();
        $query->with('user', 'post');
        $query->where('user_id', $user->id);

        if (!is_null($search)) {
            $query->whereHas('post', function ($q) use ($search) {
                $q->where('title', 'LIKE', "%$search%")
                    ->orWhere('category', 'LIKE', "%$search%")
                    ->orWhere('position', 'LIKE', "%$search%");
            });
        }
        if (!is_null($orderByRate)) {
            $query->orderBy('rate', $orderByRate);
        }
        if (!is_null($orderByDate)) {
            $query->orderBy('created_at', $orderByDate);
        }
        if (!is_null($status)) {
            $query->where('status', $status);
        }
        $query->get();

        return BidResource::collection($this->paginated($query, $request));
    }
    public function newProposal(Request $request, Post $post)
    {
        $user = auth()->user();

        DB::beginTransaction();
        try {
            $images = $request->file('images');
            $imageUrls = [];
            if ($images) {
                foreach ($images as $image) {
                    $filename = "completed_project" . "_" . time() . '_' . Str::random(10) . "." . $image->getClientOriginalExtension();
                    if (!Storage::disk('local')->exists('/completed-projects')) {
                        Storage::disk('local')->makeDirectory('/completed-projects');
                    }
                    $image->storeAs('public/completed-projects', $filename);
                    $imageUrl = asset('storage/completed-projects/' . $filename);

                    $imageUrls[] = [
                        'url' => $imageUrl,
                    ];
                }
            }

            $removedComma = str_replace(',', '', $request->rate);

            $newProposal = Bid::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
                'proposal' => $request->proposal,
                'rate' => $removedComma,
                'images' => isset($images) ? serialize($imageUrls) : null,
            ]);
            DB::commit();

            $owner = User::whereHas('posts', function ($q) use ($post) {
                $q->where('user_id', $post->user_id);
            })->get();

            // send notification to the post owner
            // $this->sendNewProposalNotification($owner, $post, $user, $newProposal);

            return response()->json([
                'code' => 200,
                'message' => "New proposal added successfully!",
                'user' => new WorkerResource($user)
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return $e;
        }
    }

    public function updateProposal(Request $request, Bid $proposal)
    {
        $user = auth()->user();

        DB::beginTransaction();
        try {
            $images = $request->file('images');
            $imageUrls = [];
            if ($images) {
                foreach ($images as $image) {
                    $filename = "completed_project" . "_" . time() . '_' . Str::random(10) . "." . $image->getClientOriginalExtension();
                    if (!Storage::disk('local')->exists('/completed-projects')) {
                        Storage::disk('local')->makeDirectory('/completed-projects');
                    }
                    $image->storeAs('public/completed-projects', $filename);
                    $imageUrl = asset('storage/completed-projects/' . $filename);

                    $imageUrls[] = [
                        'url' => $imageUrl,
                    ];
                }
            }

            $removedComma = str_replace(',', '', $request->rate);

            $proposal->update([
                'proposal' => $request->proposal,
                'rate' => $removedComma,
                'images' => isset($images) ? serialize($imageUrls) : null,
            ]);
            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => "New proposal added successfully!",
                'user' => new WorkerResource($user)
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return $e;
        }
    }

    public function choose(Request $request, Bid $proposal, Post $post)
    {
        try {
            $worker = User::find($proposal->user_id);
            $client = User::find($post->user_id);

            if ($worker->profile->availability === 'available') {
                // Check if the worker has a conflicting schedule
                $startDate = Carbon::parse($request->startDate)->format('Y-m-d');
                $endDate = Carbon::parse($request->endDate)->format('Y-m-d');

                $conflictingContracts = Contract::where('bid_id', $proposal->id)
                    ->where(function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('start_date', [$startDate, $endDate])
                            ->orWhereBetween('end_date', [$startDate, $endDate])
                            ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                                $subQuery->where('start_date', '<=', $startDate)
                                    ->where('end_date', '>=', $endDate);
                            });
                    })
                    ->get();

                if ($conflictingContracts->isEmpty()) {
                    $contract = Contract::create([
                        'post_id' => $post->id,
                        'bid_id' => $proposal->id,
                        'start_date' => $request->startDate,
                        'end_date' => $request->endDate,
                        'status' => 'in progress',
                    ]);

                    $post->update([
                        'status' => 'contracted'
                    ]);

                    // $this->sendAcceptOfferNotification($worker, $proposal, $client, $contract);

                    return response()->json([
                        'code' => 200,
                        'message' => "Successfully accepted worker's proposal"
                    ]);
                } else {
                    return response()->json([
                        'code' => 500,
                        'message' => "Worker has a conflicting schedule!"
                    ]);
                }
            } else {
                return response()->json([
                    'code' => 500,
                    'message' => "Worker is not available!"
                ]);
            }
        } catch (Exception $e) {
            return $e;
        }
    }
}
