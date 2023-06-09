<?php

namespace App\Http\Resources;

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
            'post' => $this->whenLoaded('post', function () {
                return $this->post;
            }),
            'bid' => $this->whenLoaded('bid', function () {
                return $this->bid;
            }),
            'offer' => $this->whenLoaded('offer', function () {
                return $this->offer;
            }),
            'start_date' => Carbon::parse($this->start_date)->format('F d, Y'),
            'end_date' => Carbon::parse($this->end_date)->format('F d, Y'),
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
