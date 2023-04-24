<?php

namespace App\Http\Controllers;

use App\Job;
use App\User;
use App\Skill;
use Exception;
use App\Profile;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\UserResource;
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
        return new UserResource(auth()->user());
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
        try {
            $formValues = $request->params['formValues'];
            $expertise = $request->params['expertise'];

            $user = User::where('email', $formValues['email'])->first();

            // $apiKey = env('GOOGLE_MAPS_API_KEY');
            // $client = new Client([
            //     'verify' => false
            // ]);
            // $response = $client->get("https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&key=$apiKey");
            // $data = $response->getBody()->getContents();
            // $address = $data['results'][0]['formatted_address'];
            // return response()->json([
            //     'address' => $address,
            // ]);


            $slug = Str::slug($formValues['first_name'], '-');
            $username = $slug;
            $count = 1;

            while (User::where('username', $username)->exists()) {
                $username = $slug . '-' . $count;
                $count++;
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


                $skill = new Skill;
                $skill->user_id = $user->id;
                $skill->name = $expertise['skills'];
                $skill->save();

                $job = new Job;
                $job->user_id = $user->id;
                $job->name = $expertise['jobs'];
                $job->save();

                $this->confirmRegistrationMail($user);

                $token = $user->createToken(env('APP_URL'));

                return response()->json([
                    'code' => 200,
                    'access_token' => $token->accessToken,
                    'expires_in' => $token->token->expires_at->diffInSeconds(Carbon::now()),
                    'user' => new UserResource($user),
                ]);
            }
            return response()->json([
                'code' => 500,
                'message' => 'This email is taken. Please use another email',
            ]);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function confirmedUser($id)
    {
        $user = User::where('uuid', $id)->first();

        if (Auth::user()->email_verified_at !== null) {
            return response()->json([
                'code' => 500,
                'message' => 'Greet! Your account is already identified and accepted!',
            ]);
        }

        $user->update([
            'email_verfied_at' => now(),
        ]);

        return response()->json([
            'code' => 200,
            'message' => 'Congrangulations! You are now offically a member.'
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
