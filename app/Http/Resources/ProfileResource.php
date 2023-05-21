<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'profile_link' => $this->profile_link,
            'background' => $this->background,
            'fullname' => $this->fullname,
            'profile_completeness' => $this->profile_completeness,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender,
            'address' => $this->address,
            'contact_number' => $this->contact_number,
            'profile_url' => $this->profile_url,
            'background_url' => $this->background_url,
            'daily_rate' => $this->daily_rate,
            'availability' => $this->availability,
            'facebook_url' => $this->facebook_url,
            'instagram_url' => $this->instagram_url,
            'twitter_url' => $this->twitter_url,
        ];
    }
}
