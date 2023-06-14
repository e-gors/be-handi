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
            'shortlist' => ShortlistResource::collection($this->shortlist),
            'offers' => OfferResource::collection(Offer::where('user_id', $this->id)->get()),
            'contracts' => $this->posts,
            'jobs' => $this->posts->where('status', 'posted')->all(),
            'contracts' => ContractResource::collection(Contract::with(['post.user', 'bid.user', 'offer.user'])->get())
        ];
    }
}
