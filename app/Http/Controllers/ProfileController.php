<?php

namespace App\Http\Controllers;

use App\User;
use App\Profile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ClientResource;
use App\Http\Resources\WorkerResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
    }
    public function filteredWorker($uuid)
    {
        $worker = User::whereHas('profile')->where('uuid', $uuid)->get();

        return WorkerResource::collection($worker);
    }
    public function worker(Request $request)
    {
        $search = $request->search ? $request->search : null;
        $category = $request->category ? $request->category : null;
        $location = $request->location ? $request->location : null;
        $skill = $request->skill ? $request->skill : null;
        $salaryRange = $request->salary_range ? $request->salary_range : null;

        $query = User::query();

        $query->where('role', 'Worker');

        if (!is_null($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%$search%")
                    ->orWhere('last_name', 'LIKE', "%$search%")
                    ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%$search%");
            });
        }
        if (!is_null($category)) {
            $query->whereHas('categories', function ($q) use ($category) {
                $q->whereNotNull('parent_id')->where('name', $category);
            });
        }
        if (!is_null($location)) {
            $query->whereHas('profile', function ($q) use ($location) {
                $q->where('address', 'LIKE', "%$location%");
            });
        }

        if (!is_null($skill)) {
            $query->whereHas('skills', function ($q) use ($skill) {
                $q->whereNotNull('parent_id')->where('name', $skill);
            });
        }
        if (!is_null($salaryRange)) {
            $query->whereHas('profile', function ($q) use ($salaryRange) {
                $q->where(function ($q) use ($salaryRange) {
                    $q->whereNull('rate')
                        ->orWhereBetween('rate', [0, $salaryRange]);
                });
            });
        }

        return WorkerResource::collection($this->paginated($query, $request));
    }

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
        if (!Storage::disk('local')->exists('/backgrounds')) {
            Storage::disk('local')->makeDirectory('/backgrounds');
        }
        $image->storeAs('public/backgrounds', $filename);
        $imageUrl = asset('storage/backgrounds/' . $filename);

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
        if (!Storage::disk('public')->exists('profiles')) {
            Storage::disk('public')->makeDirectory('profiles');
        }
        $image->storeAs('public/profiles', $filename);
        $imageUrl = asset('storage/profiles/' . $filename);

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
                'user' => $user->role === "Worker" ? new WorkerResource($user) : new ClientResource($user)
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
                'user' => $user->role === "Worker" ? new WorkerResource($user) : new ClientResource($user)
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
                'user' => $user->role === "Worker" ? new WorkerResource($user) : new ClientResource($user)
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
                'message' => 'Facebook link successfully deleted',
                'user' => $user->role === "Worker" ? new WorkerResource($user) : new ClientResource($user)
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
                'message' => 'Instagram link successfully deleted',
                'user' => $user->role === "Worker" ? new WorkerResource($user) : new ClientResource($user)
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
                'message' => 'Twitter link successfully deleted',
                'user' => $user->role === "Worker" ? new WorkerResource($user) : new ClientResource($user)
            ]);
        }
    }
}
