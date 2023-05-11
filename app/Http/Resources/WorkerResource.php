<?php

namespace App\Http\Resources;

use App\Profile;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkerResource extends JsonResource
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
        ];
    }
}
