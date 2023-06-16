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

    private function groupedCategories($categories)
    {
        foreach ($categories as $category) {
            if ($category->parent_id == null) {
                $nestedCategories[$category->id] = [
                    'id' => $category->id,
                    'parent_id' => $category->parent_id,
                    'name' => $category->name,
                    'children' => [],
                ];
            } else {
                $nestedCategories[$category->parent_id]['children'][] = [
                    'id' => $category->id,
                    'parent_id' => $category->parent_id,
                    'name' => $category->name,
                ];
                $categoryChildren['children'] = [
                    'name' => $category->name,
                ];
            }
        }

        return array_values($nestedCategories);
    }

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
