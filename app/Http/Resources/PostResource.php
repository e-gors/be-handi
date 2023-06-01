<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = User::find($this->user_id);

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'skills' => $this->skills ? unserialize($this->skills) : null,
            'category' => $this->category,
            'position' => $this->position,
            'job_type' => $this->job_type,
            'days' => $this->days,
            'rate' => $this->rate,
            'budget' => $this->budget,
            'locations' => $this->locations ? unserialize($this->locations) : null,
            'images' => $this->images ? unserialize($this->images) : null,
            'questions' => $this->questions ? unserialize($this->questions) : null,
            'post_url' => $this->post_url,
            'client' => new ClientResource(User::find($this->user_id)),
            'created_at' => $this->created_at->diffForHumans(),
            'total' => $user->posts->count(),
            'schedule' => $this->schedule,
        ];
    }
}
