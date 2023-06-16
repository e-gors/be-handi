<?php

namespace App\Http\Resources;

use App\Offer;
use App\Rating;
use App\Profile;
use App\Contract;
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
            'username' => $this->username,
            'fullname' => $this->fullname,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'role' => $this->role,
            'contact_number' => $this->contact_number,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'categories' => CategoryResource::collection($this->groupedCategories($this->categories)),
            'skills' => SkillResource::collection($this->groupedCategories($this->skills)),
            'categoryChildren' => CategoryResource::collection($this->categories->whereNotNull('parent_id')->toArray()),
            'skillChildren' => SkillResource::collection($this->skills->whereNotNull('parent_id')->toArray()),
            'profile' => ProfileResource::collection(Profile::where('user_id', $this->id)->get()),
            'shortlists' => $this->shortlists ? ShortlistResource::collection($this->shortlists) : null,
            'bids' => $this->bids ? ShortlistResource::collection($this->bids) : null,
            'offers' => $this->offers ? OfferResource::collection(Offer::where('profile_id', $this->id)->get()) : null,
            'projects' => $this->projects ? ProjectResource::collection($this->projects) : null,
            'contracts' => $this->contracts ? ContractResource::collection(Contract::with(['post.user', 'bid.user', 'offer.user'])->get()) : null,
            'ratings' => $this->ratings ? RatingResource::collection(Rating::where('worker_id', $this->id)->get()) : null,
            'tracker' => $this->tracker ? new TrackerResource($this->tracker) : null,
        ];
    }
}
