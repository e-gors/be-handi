<?php

namespace App\Http\Resources;

use App\Job;
use App\User;
use App\Skill;
use App\Profile;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Category;

class UserResource extends JsonResource
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
            'uuid' => $this->uuid,
            'email' => $this->email,
            'fullname' => $this->fullname,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'role' => $this->role,
            'contact_number' => $this->contact_number,
            'email_verified_at' => $this->email_verified_at,
            // 'bids' => ShortlistResource::collection($this->bids),
        ];
    }
}