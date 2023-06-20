<?php

namespace App\Http\Resources;

use App\User;
use App\Profile;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'post' => $this->post,
            'bid' => $this->whenLoaded('bid', function () {
                return $this->bid;
            }),
            'offer' => $this->whenLoaded('offer', function () {
                return $this->offer;
            }),
            'client' => User::find($this->post->user_id),
            'worker' => $this->bid ? User::find($this->bid->user_id) : User::find($this->offer->profile_id),
            'start_date' => $this->start_date->format('F d, Y'),
            'end_date' => $this->end_date->format('F d, Y'),
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
