<?php

namespace App\Http\Resources;

use App\Offer;
use App\Profile;
use App\Contract;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $id = $this->id;

        $contracts = Contract::whereHas('post.user', function ($query) use ($id) {
            $query->where('id', $id);
        })->orWhereHas('bid.user', function ($query) use ($id) {
            $query->where('id', $id);
        })->orWhereHas('offer', function ($query) use ($id) {
            $query->whereHas('user', function ($query) use ($id) {
                $query->where('id', $id);
            });
        })->get();

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'email' => $this->email,
            'fullname' => $this->fullname,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'role' => $this->role,
            'contact_number' => $this->contact_number,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'profile' => ProfileResource::collection(Profile::where('user_id', $this->id)->get()),
            'shortlists' => $this->shortlists ? ShortlistResource::collection($this->shortlists) : null,
            'offers' => $this->offers ? OfferResource::collection(Offer::where('user_id', $this->id)->get()) : null,
            'jobs' => $this->posts ? PostResource::collection($this->posts->where('status', 'posted')->all()) : null,
            'contracts' => ContractResource::collection($contracts)
        ];
    }
}
