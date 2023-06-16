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
            'post' => $this->post,
            'title' => $this->title,
            'type' => $this->type,
            'days' => $this->days,
            'rate' => $this->rate,
            'budget' => $this->budget,
            'instructions' => $this->instructions,
            'images' => unserialize($this->images),
            'status' => $this->status,
            'created_at' => $this->created_at->diffForHumans(),
            'worker' => User::find($this->profile_id),
            'client' => User::find($this->user_id)
        ];
    }
}
