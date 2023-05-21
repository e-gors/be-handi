<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use App\ShortList;
use Illuminate\Http\Request;
use App\Http\Resources\ClientResource;
use App\Http\Resources\WorkerResource;
use Exception;

class ShortlistController extends Controller
{
    public function addPostToShortlist($id)
    {
        try{
            $user = auth()->user();
            $post = Post::find($id);
    
            if ($post) {
                ShortList::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                    'favorite_type' => 'post',
                ]);
    
                return response()->json([
                    'code' => 200,
                    'message' => "Post added to your shortlist!",
                    'user' => $user->role == "Worker" ? new WorkerResource($user) : new ClientResource($user)
                ]);
            } else {
                return response()->json([
                    'code' => 500,
                    'message' => "Post not found!",
                ]);
            }
        }catch(Exception $e){
            return $e;
        }
    }
    public function removePostFromShortlist($id)
    {
        $user = auth()->user();

        $shortlist = Shortlist::where('user_id', $user->id)->where('post_id', $id)->delete();
        if (!$shortlist) {
            return response()->json([
                'code' => 403,
                'message' => 'This post is not in your shortlist!'
            ]);
        } else {
            return response()->json([
                'code' => 200,
                'message' => "Post removed in your shortlist",
                'user' => $user->role == "Worker" ? new WorkerResource($user) : new ClientResource($user)
            ]);
        }
    }
    public function addUserToShortlist($id)
    {
        $authUser = auth()->user();
        $user = User::find($id);

        if ($user) {
            ShortList::create([
                'user_id' => $authUser->id,
                'profile_id' => $user->id,
                'favorite_type' => 'user',
            ]);

            return response()->json([
                'code' => 200,
                'message' => "User added to your shortlist!",
                'user' => $user->role == "Worker" ? new WorkerResource($user) : new ClientResource($user)
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'message' => "Post not found!",
            ]);
        }
    }
}
