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

    // public function store(Request $request)
    // {
    //     $category = Skill::create([
    //         'name' => $request->input('category_name'),
    //     ]);

    //     if ($request->filled('parent_category_id')) {
    //         $parentCategory = Skil::find($request->input('parent_category_id'));
    //         $category->appendToNode($parentCategory)->save();
    //     }

    //     if ($request->filled('sub_category_name')) {
    //         $subCategory = $category->children()->create([
    //             'name' => $request->input('sub_category_name'),
    //         ]);
    //     }
    // }
}
