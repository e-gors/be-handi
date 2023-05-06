<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();

        return response()->json([
            'code ' => 200,
            'posts' => $posts
        ]);
    }

    public function post(Request $request){
        
    }
}
