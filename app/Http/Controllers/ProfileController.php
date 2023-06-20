<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use App\Skill;
use Exception;
use App\Profile;
use App\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ClientResource;
use App\Http\Resources\WorkerResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ContractorResource;

class ProfileController extends Controller
{
    public function index()
    {
        $workers = User::with(['profile', 'ratings'])
            ->where('role', 'Worker')
            ->get();

        return ContractorResource::collection($workers);
    }
    public function filteredWorker($uuid)
    {
        $worker = User::whereHas('profile')->where('uuid', $uuid)->get();

        return WorkerResource::collection($worker);
    }
    public function workers(Request $request)
    {
        $search = $request->search ? $request->search : null;
        $category = $request->category ? $request->category : null;
        $location = $request->location ? $request->location : null;
        $skill = $request->skill ? $request->skill : null;
        $salaryRange = $request->salary_range ? $request->salary_range : null;
        $userId = $request->user_id ? $request->user_id : null;

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
                        ->orWhereBetween('rate', [$salaryRange]);
                });
            });
        }

        // Exclude the authenticated user
        if (!is_null($userId)) {
            $query->where('id', '!=', $userId);
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

        $newProfile = Profile::where('user_id', $user->id)->update([
            'background' => $background
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
    public function updateAbout(Request $request)
    {
        $user = auth()->user();
        $about = $request->about;

        $newProfile = Profile::where('user_id', $user->id)->update([
            'about' => $about
        ]);

        if (!$newProfile) {
            return response()->json([
                'code' => 500,
                'message' => 'Error occured! Failed to save About.',
            ]);
        }
        return response()->json([
            'code' => 200,
            'message' => 'About successfully added!',
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

    public function updateFullname(Request $request)
    {
        $auth = auth()->user();
        $profile = Profile::where('user_id', $auth->id)->first();
        $user = User::find($auth->id);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name
        ]);

        if ($profile) {
            $profile->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'message' => "No profile has been found!",
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => "Fullname updated successfully!",
            'user' => $user->role === 'Client' ? new CLientResource($user) : new WorkerResource($user)
        ]);
    }
    public function updateAddress(Request $request)
    {
        $auth = auth()->user();
        $profile = Profile::where('user_id', $auth->id)->first();

        if ($profile) {
            $profile->update([
                'address' => $request->address,
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'message' => "No profile has been found!",
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => "Address updated successfully!",
            'user' => $auth->role === 'Client' ? new CLientResource($auth) : new WorkerResource($auth)
        ]);
    }

    public function updateEmail(Request $request)
    {

        $email = $request->email;
        $auth = auth()->user();
        $user = User::find($auth->id);
        $existingUser = User::where('email', $email)->first();

        if (empty($existingUser)) {
            $user->update([
                'email' => $email
            ]);

            return response()->json([
                'code' => 200,
                'message' => "Your email has been updated!",
                'user' => $user->role === "Client" ? new ClientResource($user) : new WorkerResource($user)
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'message' => "Email is taken!"
            ]);
        }
    }

    public function updatePhone(Request $request)
    {

        $phone = $request->contact_number;
        $auth = auth()->user();

        $user = User::find($auth->id);

        $user->update([
            'contact_number' => $phone
        ]);

        return response()->json([
            'code' => 200,
            'message' => "Your email has been updated!",
            'user' => $user->role === "Client" ? new ClientResource($user) : new WorkerResource($user)
        ]);
    }

    public function updateCategories(Request $request)
    {
        try {
            $auth = auth()->user();
            $user = User::find($auth->id);

            $categories = $request->job_categories;
            $subCategories = $request->selected_jobs;
            $categoryIds = Category::whereIn('name', $categories)->whereNull('parent_id')->pluck('id')->toArray();
            $subCategoryIds = Category::whereIn('name', $subCategories)->whereNotNull('parent_id')->pluck('id')->toArray();

            $this->attachCategoriesToUser($user, $categoryIds, $subCategoryIds);

            return response()->json([
                'code' => 200,
                'message' => "Categories successfully updated!",
                'user' => new WorkerResource($user)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function updateSkills(Request $request)
    {
        try {
            $auth = auth()->user();
            $user = User::find($auth->id);

            $categories = $request->job_categories;
            $skills = $request->selected_skills;

            $SkillCategoryIds = Skill::whereIn('name', $categories)->whereNull('parent_id')->pluck('id')->toArray();
            $skillSubCategoryIds = Skill::whereIn('name', $skills)->whereNotNull('parent_id')->pluck('id')->toArray();

            $this->attachSkillsToUser($user, $SkillCategoryIds, $skillSubCategoryIds);

            return response()->json([
                'code' => 200,
                'message' => "Categories successfully updated!",
                'user' => new WorkerResource($user)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update categories for user
    public function attachCategoriesToUser($user, $categoryIds, $subCategoryIds)
    {
        $categories = Category::whereIn('id', $categoryIds)->get();
        $subCategories = Category::whereIn('id', $subCategoryIds)->get();

        $user->categories()->sync($categories->pluck('id')->concat($subCategories->pluck('id')));
    }

    // Update skills for user
    public function attachSkillsToUser($user, $skillCategoryIds, $skillSubCategoryIds)
    {
        $skills = Skill::whereIn('id', $skillCategoryIds)->get();
        $subSkills = Skill::whereIn('id', $skillSubCategoryIds)->get();

        $user->skills()->sync($skills->pluck('id')->concat($subSkills->pluck('id')));
    }
}
