<?php

namespace App\Http\Controllers;

use App\Job;
use App\User;
use App\Skill;
use App\Profile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search ? $request->search : null;
        $query = User::query();

        $query->where('role', 'Worker')->get();

        return UserResource::collection($this->paginated($query, $request));
    }

    // public function scopeBySkillCategory($query, $search)
    // {
    //     return $query->whereHas('skills', function ($query) use ($search) {
    //         $query->where('name', 'like', "%{$search}%")
    //             ->orWhereHas('parent', function ($query) use ($search) {
    //                 $query->where('name', 'like', "%{$search}%");
    //             });
    //     });
    // }

    // public function scopeByJobCategory($query, $search)
    // {
    //     return $query->whereHas('categories', function ($query) use ($search) {
    //         $query->where('name', 'like', "%{$search}%")
    //             ->orWhereHas('parent', function ($query) use ($search) {
    //                 $query->where('name', 'like', "%{$search}%");
    //             });
    //     });
    // }


    public function uploadBGImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'background_img' => 'required|image|max:5120', // max size in kilobytes
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 500,
                'message' => $validator->errors()
            ]);
        }

        $image = $request->file('background_img');
        $filename = "background_img" . "_" . time() . '_' . Str::random(10) . "." . $image->getClientOriginalExtension();
        $image->storeAs('public/images', $filename);
        $imageUrl = asset('storage/images/' . $filename);

        $user = auth()->user();

        $newProfile = Profile::where('user_id', $user->id)->update([
            'background_url' => $imageUrl
        ]);

        if (!$newProfile) {
            return response()->json([
                'code' => 500,
                'message' => 'Error occured! Failed to save background image.',
            ]);
        }
        return response()->json([
            'code' => 200,
            'message' => 'Background image successfully added!',
            'user' => new UserResource($user),
        ]);
    }

    public function uploadProfileImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_img' => 'required|image|max:5120', // max size in kilobytes
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 500,
                'message' => $validator->errors()
            ]);
        }

        $image = $request->file('profile_img');
        $filename =  "profile_img" . "_" . time() . '_' . Str::random(10) . "." . $image->getClientOriginalExtension();
        $image->storeAs('public/images', $filename);
        $imageUrl = asset('storage/images/' . $filename);

        $user = auth()->user();

        $newProfile = Profile::where('user_id', $user->id)->update([
            'profile_url' => $imageUrl
        ]);

        if (!$newProfile) {
            return response()->json([
                'code' => 500,
                'message' => 'Error occured! Failed to save profile image.',
            ]);
        }
        return response()->json([
            'code' => 200,
            'message' => 'Profile image successfully added!',
            'user' => new UserResource($user),
        ]);
    }

    public function worker()
    {
        $users = User::where('role', 'Worker')->whereHas('profile')->get();
        return UserResource::collection($users);
    }
}
