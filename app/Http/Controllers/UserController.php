<?php

namespace App\Http\Controllers;

use App\Job;
use App\User;
use App\Skill;
use Exception;
use App\Profile;
use App\Category;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            if (auth()->attempt($request->only(['email', 'password']))) {
                $user = auth()->user();

                $token = $user->createToken(env('APP_URL'));

                return response()->json([
                    'code' => 200,
                    'access_token' => $token->accessToken,
                    'expires_in' => $token->token->expires_at->diffInSeconds(Carbon::now()),
                    'user' => new UserResource($user)
                ]);
            } else {
                return response()->json([
                    'code' => 500,
                    'message' => 'Invalid Credentials!'
                ]);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function getUser()
    {
        $user = auth()->user();
        return new UserResource($user);
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $role)
    {
        DB::beginTransaction();
        try {
            if ($role === 'Worker') {
                $formValues = $request->params['formValues'];

                $user = User::where('email', $formValues['email'])->first();

                $username = strtolower($formValues['first_name']);

                while (User::where('username', $username)->exists()) {
                    $username  = $formValues['first_name'] . "_" . $formValues['last_name'];
                }

                if (empty($user)) {
                    $user = User::create([
                        'uuid' => Str::uuid(),
                        'email' => $formValues['email'],
                        'username' => $username,
                        'first_name' => $formValues['first_name'],
                        'last_name' => $formValues['last_name'],
                        'contact_number' => $formValues['number'],
                        'role' => $role,
                        'password' => Hash::make($formValues['password'])
                    ]);

                    Profile::create([
                        'user_id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'gender' => $formValues['gender'],
                        'address' => $formValues['address'],
                    ]);

                    $categories = $request->params['expertise']['job_categories'];
                    $subCategories = $request->params['expertise']['selected_jobs'];
                    $skills = $request->params['expertise']['selected_skills'];

                    $categoryIds = Category::whereIn('name', $categories)->whereNull('parent_id')->pluck('id')->toArray();
                    $subCategoryIds = Category::whereIn('name', $subCategories)->whereNotNull('parent_id')->pluck('id')->toArray();
                    $SkillCategoryIds = Skill::whereIn('name', $categories)->whereNull('parent_id')->pluck('id')->toArray();
                    $skillSubCategoryIds = Skill::whereIn('name', $skills)->whereNotNull('parent_id')->pluck('id')->toArray();

                    foreach ($categoryIds as $categoryId) {
                        $category = Category::find($categoryId);

                        if ($category) {
                            $user->categories()->attach($category->id);
                            foreach ($subCategoryIds as $subCategoryId) {
                                $subCategory = Category::find($subCategoryId);

                                if ($subCategory && $subCategory->isDescendantOf($category)) {
                                    $user->categories()->attach($subCategory->id);
                                }
                            }
                        }
                    }

                    foreach ($SkillCategoryIds as $skillCategoryId) {
                        $skill = Skill::find($skillCategoryId);

                        if ($skill) {
                            $user->skills()->attach($skill->id);
                            foreach ($skillSubCategoryIds as $skillsSubCategoryId) {
                                $subSkill = Skill::find($skillsSubCategoryId);

                                if ($subSkill && $subSkill->isDescendantOf($skill)) {
                                    $user->skills()->attach($subSkill->id);
                                }
                            }
                        }
                    }

                    DB::commit();
                    // $this->confirmRegistrationMail($user);

                    return response()->json([
                        'code' => 200,
                        'message' => 'Congratulations! You are now part of the team. Please see you email for verfification.',
                        'user' => $user,
                    ]);
                }
                return response()->json([
                    'code' => 500,
                    'message' => 'This email is taken. Please use another email',
                ]);
            } else {
                $user = User::where('email', $request->email)->first();

                $username = Str::slug($request->first_name, '-' . uniqid());


                if (empty($user)) {
                    $user = User::create([
                        'uuid' => Str::uuid(),
                        'email' => $request->email,
                        'username' => $username,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'contact_number' => $request->number,
                        'role' => $role,
                        'password' => Hash::make($request->password)
                    ]);

                    Profile::create([
                        'user_id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'gender' => $request->gender,
                        'address' => $request->address,
                    ]);

                    DB::commit();

                    $this->confirmRegistrationMail($user);

                    return response()->json([
                        'code' => 200,
                        'message' => 'Congratulations! You are now part of the team.',
                        'user' => $user,
                    ]);
                }
                return response()->json([
                    'code' => 500,
                    'message' => 'This email is taken. Please use another email'
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    public function confirmedUser($id)
    {
        $user = User::where('uuid', $id)->first();

        if (!$user) {
            return response()->json([
                'code' => 404,
                'message' => "User not found!"
            ]);
        }
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'code' => 400,
                'message' => "Email already verified! Please proceed to login"
            ]);
        }

        $user->email_verified_at = now();
        $user->save();

        return response()->json([
            'code' => 200,
            'message' => "Your account is successfuly verified!",
        ]);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
