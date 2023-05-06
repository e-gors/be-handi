<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public Routes
Route::get('posts', 'PostController@index');
Route::post('login', 'UserController@login');
Route::post('join-us/{role}', 'UserController@store');
Route::get('verify-email/{id}', 'UserController@confirmedUser');
Route::get('categories', 'CategoryController@categories');
Route::get('skills', 'SkillController@skills');
Route::get('workers', 'ProfileController@index');

//Private Routes
Route::middleware('auth:api')->group(function () {
    Route::get('get-user', 'UserController@getUser');
    Route::post('post-jobs/{id}', 'PostController@post');
    Route::post('upload/bg-image', 'ProfileController@uploadBGImage');
    Route::post('upload/profile-image', 'ProfileController@uploadProfileImage');
});
