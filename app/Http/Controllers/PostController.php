<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class PostController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->search ? $request->search : null;
            $category = $request->category ? $request->category : null;
            $location = $request->location ? $request->location : null;
            $skill = $request->skill ? $request->skill : null;
            $salaryRange = $request->salary_range ? $request->salary_range : null;
            // $shortlisted = $request->shortlisted ? $request->shortlisted : null;

            $query = Post::query();
            $query->where('visibility', 'Public');
            $query->where('status', 'posted');

            if (!is_null($search)) {
                $query->join('users', 'posts.user_id', '=', 'users.id')
                    ->where(function ($query) use ($search) {
                        $query->where('users.first_name', 'LIKE', "%$search%")
                            ->orWhere('users.last_name', 'LIKE', "%$search%")
                            ->orWhere(DB::raw("CONCAT(users.first_name, ' ', users.last_name)"), 'LIKE', "%$search%")
                            ->orWhere('posts.position', 'LIKE', "%$search%")
                            ->orWhere('posts.skills', 'LIKE', '%' . $search . '%');
                    });
            }

            if (!is_null($category)) {
                $query->where('position', $category);
            }

            if (!is_null($location)) {
                $query->where('locations', 'LIKE', '%' . $location . '%');
            }

            if (!is_null($skill)) {
                $query->where('skills', 'LIKE', '%' . $skill . '%');
            }
            // if (!is_null($shortlisted)) {
            //     if ($shortlisted == 'true') {
            //         $query->whereHas('shortlist');
            //     } elseif ($shortlisted == 'false') {
            //         $query->whereDoesntHave('shortlist');
            //     }
            // }

            // if (!is_null($salaryRange)) {
            //     $query->where(function ($q) use ($salaryRange) {
            //         $q->whereNull('rate')
            //             ->orWhereBetween('rate', [0, $salaryRange]);
            //     });
            // }

            $query->orderBy('posts.created_at', 'desc');
            return PostResource::collection($this->paginated($query, $request));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function post(Request $request)
    {
        DB::beginTransaction();
        try {
            $authUser = auth()->user();
            $title = $request->title;
            $description = $request->description;
            $typeAndBudget = json_decode($request->typeAndBudget);
            $images = $request->file('images');
            $questions = json_decode($request->questions);
            $skills = json_decode($request->skills);
            $locations = json_decode($request->locations);
            $visibility = $request->status;

            $imageUrls = [];
            if ($images) {
                foreach ($images as $image) {
                    $filename = "post_img" . "_" . time() . '_' . Str::random(10) . "." . $image->getClientOriginalExtension();
                    if (!Storage::disk('local')->exists('/posts')) {
                        Storage::disk('local')->makeDirectory('/posts');
                    }
                    $image->storeAs('public/posts', $filename);
                    $imageUrl = asset('storage/posts/' . $filename);

                    $imageUrls[] = [
                        'url' => $imageUrl,
                    ];
                }
            }

            if (isset($typeAndBudget[0]->rate)) {
                $removedComma = str_replace(',', '', $typeAndBudget[0]->rate);
            } else {
                $removedComma = str_replace(',', '', $typeAndBudget[0]->budget);
            }

            $uuid = Str::uuid();
            $newPost = Post::create([
                'user_id' => $authUser->id,
                'uuid' => $uuid,
                'title' => $title,
                'description' => $description,
                'skills' => isset($skills) ? serialize($skills) : null,
                'category' =>  $typeAndBudget[0]->category,
                'position' =>  $typeAndBudget[0]->sub_category,
                'job_type' =>  $typeAndBudget[0]->type,
                'days' =>  isset($typeAndBudget[0]->days) ? $typeAndBudget[0]->days : null,
                'rate' =>  isset($typeAndBudget[0]->rate) ? $removedComma : null,
                'budget' =>  isset($typeAndBudget[0]->budget) ? $removedComma : null,
                'locations' => isset($locations) ? serialize($locations) : null,
                'images' => isset($images) ? serialize($imageUrls) : null,
                'questions' => isset($questions) ? serialize($questions) : null,
                'post_url' => env('APP_BASE_URL') . 'posts/' . $uuid,
                'visibility' => $visibility,
            ]);
            Db::commit();

            $category = $typeAndBudget[0]->category;

            if ($newPost) {
                $matchedUsers = User::whereHas('categories', function ($query) use ($category) {
                    $query->where('name', $category);
                })->get()->toArray();

                $postResource = new PostResource($newPost);

                if ($matchedUsers !== null && !empty($matchedUsers)) {
                    foreach ($matchedUsers as $user) {
                        // $this->sendNewJobPostNotification($user, $postResource, $authUser);
                    }
                }

                return response()->json([
                    'code' => 200,
                    'message' => "New job has been posted!",
                    'url' => '/posts/' . $uuid,
                ]);
            }

            DB::rollBack();
            return response()->json([
                'code' => 500,
                'message' => "Failed to create new post!"
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    public function recommendedJobs(Request $request)
    {
        $user = auth()->user();
        $query = Post::query();
        $categories = $user->categories;
        $categoryNames = $categories->pluck('name')->toArray();

        $query->whereIn('category', $categoryNames)->get();

        return PostResource::collection($this->paginated($query, $request));
    }
}
