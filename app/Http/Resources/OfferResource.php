<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
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
            'user_id' => $this->user_id,
            'profile_id' => $this->profile_id,
            'post_id' => $this->post_id,
            'status' => $this->status,
            'created_at' => $this->created_at->diffForHumans(),
            'job' => $this->post,
            'worker' => User::find($this->profile_id),
            'client' => $this->user,
        ];
    }
}
