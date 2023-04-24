<?php

namespace App\Http\Resources;

use App\Job;
use App\Skill;
use App\Profile;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Resources\Json\JsonResource;

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
            // 'profile' => new ProfileResource(Profile::where('user_id', $this->id)->first()),
            'skill' => Skill::where('user_id', $this->id)->first(),
            'job' => Job::where('user_id', $this->id)->first(),
            'profile' => Profile::where('user_id', $this->id)->first(),
        ];
    }
}
