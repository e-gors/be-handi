<?php

namespace App\Http\Controllers;

use App\Bid;
use App\Post;
use App\Offer;
use Exception;
use App\Contract;
use App\Schedule;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OfferResource;
use App\Http\Resources\WorkerResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search ? $request->search : null;
        $status = $request->status ? $request->status : null;
        $type = $request->type ? $request->type : null;

        $user = auth()->user();
        $query = Offer::query();

        if ($user->role === 'Client') {
            $query->where('user_id', $user->id);
        } else {
            $query->where('profile_id', $user->id);
        }

        if (!is_null($search)) {
            $query->join('users', 'offers.user_id', '=', 'users.id')
                ->where(function ($query) use ($search) {
                    $query->where('users.first_name', 'LIKE', "%$search%")
                        ->orWhere('users.last_name', 'LIKE', "%$search%")
                        ->orWhere(DB::raw("CONCAT(users.first_name, ' ', users.last_name)"), 'LIKE', "%$search%");
                });
        }

        if (!is_null($status)) {
            $query->where('status', $status);
        }
        if (!is_null($type)) {
            $query->where('type', $type);
        }

        return OfferResource::collection($this->paginated($query, $request));
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $formValues = json_decode($request->formValues);
            $images = $request->file('images') ? $request->file('images') : null;
            $instruction = $request->instruction ? $request->instruction : null;
            $worker = $request->worker ? json_decode($request->worker) : null;

            if (empty($worker)) {
                return response()->json([
                    'code' => 500,
                    'message' => "Contractor field is required!"
                ]);
            }

            $imageUrls = [];

            if (!empty($images)) {
                foreach ($images as $image) {
                    $filename = "offer_img" . "_" . time() . '_' . Str::random(10) . "." . $image->getClientOriginalExtension();
                    if (!Storage::disk('local')->exists('/offers')) {
                        Storage::disk('local')->makeDirectory('/offers');
                    }
                    $image->storeAs('public/offers', $filename);
                    $imageUrl = asset('storage/offers/' . $filename);

                    $imageUrls[] = [
                        'url' => $imageUrl,
                    ];
                }
            }

            $post = Post::find($formValues[0]->post);

            if (isset($formValues[0]->rate)) {
                $removedComma = str_replace(',', '', $formValues[0]->rate);
            } else {
                $removedComma = str_replace(',', '', $formValues[0]->budget);
            }

            $newOffer = Offer::create([
                'user_id' => $user->id,
                'profile_id' => $worker[0]->id,
                'post_id' => $formValues[0]->post ? $formValues[0]->post : null,
                'type' => $formValues[0]->type,
                'days' => isset($formValues[0]->days) ? $formValues[0]->days : null,
                'rate' => isset($formValues[0]->rate) ? $removedComma : null,
                'budget' => isset($formValues[0]->budget) ? $removedComma : null,
                'instructions' => isset($instruction) ? $instruction : null,
                'images' => isset($images) ? serialize($imageUrls) : null,
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
}
