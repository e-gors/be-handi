<?php

namespace App\Http\Controllers;

use App\User;
use App\WorkExperience;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\WorkerResource;

class WorkExperienceController extends Controller
{
    private function convertDate($data)
    {
        return Carbon::parse($data)->format('Y-m-d H:i:s');
    }
    public function store(Request $request)
    {
        $startDate = $this->convertDate($request->start_date);
        $endDate = $this->convertDate($request->end_date);
        $auth = auth()->user();
        $user = User::find($auth->id);
        $newWorkExperience = WorkExperience::create([
            'user_id' => $user->id,
            'position' => $request->position,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'notes' => $request->notes,
        ]);

        if ($newWorkExperience) {
            return response()->json([
                'code' => 200,
                'message' => 'Work Experience added successfully!',
                'user' => new WorkerResource($auth)
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'message' => 'Failed to add work experience!',
            ]);
        }
    }

    public function update(Request $request, WorkExperience $experience)
    {
        return $request;
        
        $startDate = $this->convertDate($request->start_date);
        $endDate = $this->convertDate($request->end_date);
        $auth = auth()->user();
        $user = User::find($auth->id);
        $updatedExperience = $experience->update([
            'position' => $request->position,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'notes' => $request->notes,
        ]);

        if ($updatedExperience) {
            return response()->json([
                'code' => 200,
                'message' => 'Work Experience updated successfully!',
                'user' => new WorkerResource($user)
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'message' => 'Failed to update work experience!',
            ]);
        }
    }
}
