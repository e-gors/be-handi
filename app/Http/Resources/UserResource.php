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
            'categories' => CategoryResource::collection($this->groupedCategories($this->categories)),
            'skills' => SkillResource::collection($this->groupedCategories($this->skills)),
            'categoryChildren' => CategoryResource::collection($this->categories->whereNotNull('parent_id')->toArray()),
            'skillChildren' => SkillResource::collection($this->skills->whereNotNull('parent_id')->toArray()),
            'profile' => ProfileResource::collection(Profile::where('user_id', $this->id)->get()),
            'shortlist' => ShortlistResource::collection($this->shortlist),
            // 'bids' => ShortlistResource::collection($this->bids),
        ];
    }
}