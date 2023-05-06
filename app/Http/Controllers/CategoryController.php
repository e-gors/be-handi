<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function categories()
    {
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return CategoryResource::collection($categories);
    }

    // public function store(Request $request)
    // {
    //     $category = Category::create([
    //         'name' => $request->input('category_name'),
    //     ]);

    //     if ($request->filled('parent_category_id')) {
    //         $parentCategory = Category::find($request->input('parent_category_id'));
    //         $category->appendToNode($parentCategory)->save();
    //     }

    //     if ($request->filled('sub_category_name')) {
    //         $subCategory = $category->children()->create([
    //             'name' => $request->input('sub_category_name'),
    //         ]);
    //     }
    // }
}
