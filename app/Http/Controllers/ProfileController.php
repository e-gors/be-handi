<?php

namespace App\Http\Controllers;

use App\Job;
use App\User;
use App\Skill;
use App\Profile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\ClientResource;
use App\Http\Resources\WorkerResource;
use App\SocialNetwork;
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
    public function worker()
    {
        $users = User::where('role', 'Worker')->whereHas('profile')->get();
        return UserResource::collection($users);
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
                'message' => 'The maximum file size limit is 5mb. Try to select another image'
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
                'code' => 401,
                'message' => 'Error occured! Failed to save background image.',
            ]);
        }
        return response()->json([
            'code' => 200,
            'message' => 'Background image successfully added!',
            'user' => $user->role === 'Client' ? new ClientResource($user) : new WorkerResource($user),
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
                'message' => 'The maximum file size limit is 5mb. Try to select another image.'
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
            'user' => $user->role === 'Client' ? new ClientResource($user) : new WorkerResource($user),
        ]);
    }

    public function updateBackground(Request $request)
    {
        $user = auth()->user();
        $background = $request->background;
        $messageWithBr = nl2br($background);

        $newProfile = Profile::where('user_id', $user->id)->update([
            'background' => $messageWithBr
        ]);

        if (!$newProfile) {
            return response()->json([
                'code' => 500,
                'message' => 'Error occured! Failed to save background.',
            ]);
        }
        return response()->json([
            'code' => 200,
            'message' => 'Background successfully added!',
            'user' => $user->role === 'Client' ? new ClientResource($user) : new WorkerResource($user),
        ]);
    }
    public function updateSocialNetwork(Request $request)
    {
        $user = auth()->user();
        $facebook = $request->facebook;
        $twitter = $request->twitter;
        $instagram = $request->instagram;

        $profile = Profile::query();
        $profile->where('user_id', $user->id);

        if ($facebook) {
            $newProfile = $profile->update([
                'facebook_url' => $facebook
            ]);

            if (!$newProfile) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Error occured! Failed to save Facebook link.',
                ]);
            }
            return response()->json([
                'code' => 200,
                'message' => 'Facebook link successfully added!',
                'user' => new WorkerResource($user),
            ]);
        }

        if ($twitter) {
            $newProfile = $profile->update([
                'twitter_url' => $twitter
            ]);

            if (!$newProfile) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Error occured! Failed to save Twitter link.',
                ]);
            }
            return response()->json([
                'code' => 200,
                'message' => 'Twitter link successfully added!',
                'user' => new WorkerResource($user),
            ]);
        }
        if ($instagram) {
            $newProfile = $profile->update([
                'instagram_url' => $instagram
            ]);

            if (!$newProfile) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Error occured! Failed to save Instagram link.',
                ]);
            }
            return response()->json([
                'code' => 200,
                'message' => 'Instagram link successfully added!',
                'user' => new WorkerResource($user),
            ]);
        }
    }

    public function removeSocialNetworks($params)
    {
        $user = auth()->user();

        $profile = Profile::query();
        $profile->where('user_id', $user->id);

        if ($params === 'Facebook') {
            $newProfile = $profile->update([
                'facebook_url' => null
            ]);

            if (!$newProfile) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Failed to remove Facebook link!'
                ]);
            }
            return response()->json([
                'code' => 200,
                'message' => 'Facebook link successfully deleted'
            ]);
        }
        if ($params === 'Instagram') {
            $newProfile = $profile->update([
                'instagram_url' => null
            ]);
            if (!$newProfile) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Failed to remove Instagram link!'
                ]);
            }
            return response()->json([
                'code' => 200,
                'message' => 'Instagram link successfully deleted'
            ]);
        }
        if ($params === 'Twitter') {
            $newProfile = $profile->update([
                'twitter_url' => null
            ]);
            if (!$newProfile) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Failed to remove Twitter link!'
                ]);
            }
            return response()->json([
                'code' => 200,
                'message' => 'Twitter link successfully deleted'
            ]);
        }
    }
}
