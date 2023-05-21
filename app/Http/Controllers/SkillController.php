<?php

namespace App\Http\Controllers;

use App\Http\Resources\SkillResource;
use App\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function skills()
    {
        $skill = Skill::with('children')->whereNull('parent_id')->get();
        return SkillResource::collection($skill);
    }

    public function children()
    {
        $children = SKill::whereNotNull('parent_id')->get();
        return SkillResource::collection($children);
    }
}
