<?php

namespace App\Http\Resources;

use App\Profile;
use Illuminate\Http\Resources\Json\JsonResource;

class BidResource extends JsonResource
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
            'post_id' => $this->post_id,
            'proposal' => $this->proposal,
            'rate' => $this->rate,
            'status' => $this->status,
            'images' => unserialize($this->images),
            'created_at' => $this->created_at,
            'post' => $this->post,
            'user' => new WorkerResource($this->user),
            'profile' => Profile::where('user_id', $this->user_id)->first(),
        ];
    }
}
