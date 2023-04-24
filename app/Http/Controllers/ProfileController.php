<?php

namespace App\Http\Controllers;

use App\Job;
use App\User;
use App\Skill;
use App\Profile;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function store(Request $request)
    {

        $image = $request->file('image');
        $filename = time() . '_' . $image->getClientOriginalName();
        $path = $image->storeAs('public/images', $filename);
        $imageUrl = asset('storage/images/' . $filename);


        $user = auth()->user();

        $data = json_decode($request->basicInfo, true);

        User::find($user->id)->update([
            'role' => $request->selectedRole,
            'contact_number' => $data['values']['contact_number'],
        ]);

        $newProfile = Profile::create([
            'user_id' => $user->id,
            'background' => $data['values']['background'],
            'profile_url' => $imageUrl,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'gender' => $data['values']['gender'],
            'address' => $user->address ? $user->address : 'Hilongos, Leyte',
        ]);

        $newSkills = new Skill;
        $newSkills->user_id = $user->id;
        $newSkills->array_column = $request->skills;
        $newSkills->save();

        $newJobs = new Job;
        $newJobs->user_id = $user->id;
        $newJobs->array_column = $request->jobs;
        $newJobs->save();

        return response()->json([
            'code' => 200,
            'user' => $user,
            'profile' => $newProfile,
            'skills' => $newSkills,
            'jobs' => $newJobs,
        ]);
    }

    public function uploadImage(Request $request)
    {
        $image = $request->file('image');
        $filename = time() . '_' . $image->getClientOriginalName();
        $image->storeAs('public/images', $filename);
        $imageUrl = asset('storage/images/' . $filename);

        $user = auth()->user();

        $imageUrl = Profile::where('user_id', $user->id)->update([
            'profile_url' => $imageUrl
        ]);

        return response()->json([
            'code' => 200,
            'image' => $imageUrl
        ]);
    }

    public function worker(){
        $users = User::where('role', 'Worker')->whereHas('profile')->get();

        return UserResource::collection($users);
    }
}
