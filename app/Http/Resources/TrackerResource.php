<?php

namespace App\Http\Resources;

use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackerResource extends JsonResource
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
            'user_id' => $this->user_ud,
            'profile_view' => $this->profile_view,
            'search_result' => $this->search_result,
            'created_at' => $this->created_at,
            'profile_view_w' => $this->where('created_at', '>=', Carbon::now()->startOfWeek())->sum('profile_view'),
            'profile_view_m' => $this->where('created_at', '>=', Carbon::now()->startOfMonth())->sum('profile_view'),
            'search_result_w' => $this->where('created_at', '>=', Carbon::now()->startOfWeek())->sum('search_result'),
            'search_result_m' => $this->where('created_at', '>=', Carbon::now()->startOfMonth())->sum('search_result'),
        ];
    }
}
