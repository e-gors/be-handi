<?php

namespace App\Http\Controllers;

use App\Post;
use App\Offer;
use Exception;
use App\Contract;
use App\Schedule;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OfferResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search ? $request->search : null;
        $status = $request->status ? $request->status : null;

        $query = Offer::query();

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

        return OfferResource::collection($this->paginated($query, $request));
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $formValues = json_decode($request->formValues);
            $images = $request->file('images') ? $request->file('images') : null;
            $instruction = $request->instruction ? $request->instruction : null;
            $workers = $request->worker ? json_decode($request->worker) : null;

            if (empty($workers)) {
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

            $newOffer = Offer::create([
                'user_id' => $user->id,
                'profile_id' => $workers[0]->id,
                'post_id' => $formValues[0]->post ? $formValues[0]->post : null,
                'title' => $formValues[0]->title ? $formValues[0]->title : $post->title,
                'type' => $formValues[0]->type,
                'days' => isset($formValues[0]->days) ? $formValues[0]->days : null,
                'rate' => isset($formValues[0]->rate) ? $formValues[0]->rate : null,
                'budget' => isset($formValues[0]->budget) ? $formValues[0]->budget : null,
                'instruction' => isset($instruction) ? $instruction : null,
                'images' => isset($images) ? serialize($imageUrls) : null,
            ]);

            if (!$newOffer) {
                return response()->json([
                    'code' => 500,
                    'message' => "Encounter Error while saving your offer!"
                ]);
            } else {
                // $this->sendNewOfferNotification($workers, $newOffer, $user);

                return response()->json([
                    'code' => 200,
                    'message' => "Successfully send new offer to User"
                ]);
            }
        } catch (Exception $e) {
            return $e;
        }
    }

    // public function accept(Request $request, Offer $offer)
    // {
    //     $user = auth()->user();

    //     $schedule = Schedule::find(1);

    //     $newContract = Contract::create([
    //         'offer_id' => $offer->id,
    //         'schedule_id' => $schedule->id
    //     ]);
    // }
}
