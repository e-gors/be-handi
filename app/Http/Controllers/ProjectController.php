<?php

namespace App\Http\Controllers;

use Exception;
use App\Project;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\ProjectResource;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $images = $request->file('images');

            $imageUrls = [];
            if ($images) {
                foreach ($images as $image) {
                    $filename = "completed_project" . "_" . time() . '_' . Str::random(10) . "." . $image->getClientOriginalExtension();
                    if (!Storage::disk('local')->exists('/completed-projects')) {
                        Storage::disk('local')->makeDirectory('/completed-projects');
                    }
                    $image->storeAs('public/completed-projects', $filename);
                    $imageUrl = asset('storage/completed-projects/' . $filename);

                    $imageUrls[] = [
                        'url' => $imageUrl,
                    ];
                }
            }

            foreach ($imageUrls as $image) {
                $newProjects = Project::create([
                    'user_id' => $user->id,
                    'image' => $image['url'],
                ]);
            };

            if (!$newProjects) {
                return response()->json([
                    'code' => 500,
                    'message' => "Failed to upload image. Please try again!",
                ]);
            }

            $projects = $user->projects()->get();

            return response()->json([
                'code' => 200,
                'projects' => ProjectResource::collection($projects),
            ]);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function destroy(Project $id)
    {
        $user = auth()->user();
        $id->delete();
        $projects = $user->projects()->get();

        return response()->json([
            'code' => 200,
            'message' => "Image deleted successfully!",
            'projects' => ProjectResource::collection($projects)
        ]);
    }
}
