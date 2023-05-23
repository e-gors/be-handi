<?php

namespace App\Http\Controllers;

use App\Offer;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $query = Offer::query();
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();

            $formValues = json_decode($request->formValues);
            $images = $request->file('images');
            $instruction = $request->instruction;
            $workers = json_decode($request['worker']);
            $worker = $workers[0];
            $post = $request->post;

            if (empty($worker)) {
                return response()->json([
                    'code' => 500,
                    'message' => "Contractor field is required!"
                ]);
            }
            if (empty($post)) {
                return response()->json([
                    'code' => 500,
                    'message' => "Post field is required!"
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

            $newOffer = Offer::create([
                'user_id' => $user->id,
                'profile_id' => $worker->id,
                'post_id' => $post->id,
                'title' => $formValues[0]->title,
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
                $this->sendNewOfferNotification($worker, $newOffer, $user);

                return response()->json([
                    'code' => 200,
                    'message' => "Successfully send new offer notifications"
                ]);
            }
        } catch (Exception $e) {
            return $e;
        }
    }
}
