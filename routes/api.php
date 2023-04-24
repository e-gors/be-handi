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

//profiles(worker)
Route::get('workers', 'ProfileController@worker');

//Public Routes
Route::post('login', 'UserController@login');
Route::post('join-us/{role}', 'UserController@store');

//Private Routes
Route::middleware('auth:api')->group(function () {
    //users
    Route::get('get-user', 'UserController@getUser');

    //register user
    //confirm user
    Route::get('confirmed/{id}', 'UserController@confirmedUser');

});
