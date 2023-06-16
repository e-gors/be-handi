<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

class JobPostController extends Controller
{
    public function jobPost(){
        $jobPost = Post::query();
        return $jobPost->with('schedule')->get();
    }
}