<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
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
            'user_id' => $this->user_id,
            'worker_id' => $this->worker_id,
            'comment' => $this->comment,
            'rating' => $this->rating,
            'created_at' => $this->created_at->diffForHumans(),
            'client' => new ClientResource(User::find($this->user_id)),
            'rating_count_w' => $this->where('created_at', '>=', Carbon::now()->startOfWeek())->sum('rating'),
            'rating_count_m' => $this->where('created_at', '>=', Carbon::now()->startOfMonth())->sum('rating'),
        ];
    }
}
