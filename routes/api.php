<?php

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
Route::post('login', 'UserController@login');
Route::post('join-us/{role}', 'UserController@store');
Route::post('apply/register', 'UserController@registerOnApply');
Route::get('verify-email/{id}', 'UserController@confirmedUser');
Route::get('categories', 'CategoryController@categories');
Route::get('skills', 'SkillController@skills');
Route::get('workers', 'ProfileController@worker');
Route::get('skills/children', 'SkillController@children');
Route::get('jobs', 'PostController@index');
Route::get('locations', 'LocationController@index');
Route::get('worker/{uuid}', 'ProfileController@filteredWorker');
Route::get('/user/workers', 'ProfileController@index');
// Route::post('/send/sms', 'Controller@sendSms');

//Private Routes
Route::middleware('auth:api')->group(function () {
    Route::get('get-user', 'UserController@getUser');
    Route::post('new/job-post', 'PostController@post');
    Route::post('upload/bg-image', 'ProfileController@uploadBGImage');
    Route::post('upload/profile-image', 'ProfileController@uploadProfileImage');
    Route::post('update/background', 'ProfileController@updateBackground');
    Route::post('update/social-networks', 'ProfileController@updateSocialNetwork');
    Route::delete('delete/social-networks/{params}', 'ProfileController@removeSocialNetworks');
    Route::post('new/proposal/{post}', 'ProposalController@newProposal');
    Route::post('update/proposal/{proposal}', 'ProposalController@updateProposal');
    Route::post('new/shortlist/post/{id}', 'ShortlistController@addPostToShortlist');
    Route::delete('remove/shortlist/post/{id}', 'ShortlistController@removePostFromShortlist');
    Route::post('new/shortlist/user/{id}', 'ShortlistController@addUserToShortlist');
    Route::get('proposals', 'ProposalController@index');
    Route::get('user/proposals', 'ProposalController@userBids');
    Route::get('offers', 'OfferController@index');
    Route::post('offer/accept/{offer}', 'OfferController@accept');
    Route::get('recommended/jobs', 'PostController@recommendedJobs');
    Route::post('new/job-offer', 'OfferController@store');
    Route::post('ratings', 'RatingController@store');
    Route::get('reviews/{uuid}', 'RatingController@getReviews');
    Route::post('new/projects', 'ProjectController@store');
    Route::delete('projects/{id}', 'ProjectController@destroy');
    Route::get('projects', 'ProjectController@index');
    Route::post('choose/proposal/{proposal}/{post}', 'ProposalController@choose');
});
