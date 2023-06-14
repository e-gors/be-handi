<?php

namespace App\Http\Resources;

use App\Rating;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractorResource extends JsonResource
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
            'fullname' => $this->fullname,
            'email' => $this->email,
            'ratings' => RatingResource::collection(Rating::where('worker_id', $this->id)->get()),
            'profile' => new ProfileResource($this->profile)
        ];
    }
}
