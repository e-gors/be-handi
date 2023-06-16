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
Route::get('categories', 'CategoryController@categories');
Route::get('skills', 'SkillController@skills');
Route::get('workers', 'ProfileController@workers');
Route::get('skills/children', 'SkillController@children');
Route::get('jobs', 'PostController@index');
Route::get('locations', 'LocationController@index');
Route::get('worker/{uuid}', 'ProfileController@filteredWorker');
Route::get('user/workers', 'ProfileController@index');
// Route::post('/send/sms', 'Controller@sendSms');
Route::get('reviews/{client}', 'RatingController@getReviews');
Route::post('track/user/view/{uuid}', 'TrackerController@profileView');

//Private Routes
Route::middleware('auth:api')->group(function () {
    //users
    Route::get('get-user', 'UserController@getUser');
    Route::post('new/shortlist/user/{id}', 'ShortlistController@addUserToShortlist');
    Route::get('verify-email/{id}', 'UserController@confirmedUser');
    Route::post('account/update/password', 'UserController@updatePassword');
    Route::delete('account/terminate', 'UserController@destroy');

    //jobs
    Route::post('new/job-post', 'PostController@post');
    Route::post('new/shortlist/post/{id}', 'ShortlistController@addPostToShortlist');
    Route::delete('remove/shortlist/post/{id}', 'ShortlistController@removePostFromShortlist');
    Route::get('recommended/jobs', 'PostController@recommendedJobs');
    Route::get('user/jobs', 'PostController@userPosts');

    //proposals
    Route::post('new/proposal/{post}', 'ProposalController@newProposal');
    Route::post('update/proposal/{proposal}', 'ProposalController@updateProposal');
    Route::get('proposals', 'ProposalController@index');
    Route::get('user/proposals', 'ProposalController@userBids');
    Route::post('choose/proposal/{proposal}/{post}', 'ProposalController@choose');

    //offers
    Route::get('offers', 'OfferController@index');
    Route::post('offer/accept/{offer}', 'OfferController@accept');
    Route::post('new/job-offer', 'OfferController@store');

    //profiles
    Route::post('upload/bg-image', 'ProfileController@uploadBGImage');
    Route::post('upload/profile-image', 'ProfileController@uploadProfileImage');
    Route::post('update/background', 'ProfileController@updateBackground');
    Route::post('new/projects', 'ProjectController@store');
    Route::post('update/fullname', 'ProfileController@updateFullname');
    Route::post('update/address', 'ProfileController@updateAddress');
    Route::get('projects', 'ProjectController@index');
    Route::post('update/about', 'ProfileController@updateAbout');
    Route::post('update/social-networks', 'ProfileController@updateSocialNetwork');
    Route::delete('delete/social-networks/{params}', 'ProfileController@removeSocialNetworks');
    Route::delete('projects/{id}', 'ProjectController@destroy');
<<<<<<< Updated upstream
    Route::post('account/update/email', 'ProfileController@updateEmail');
    Route::post('account/update/phone', 'ProfileController@updatePhone');
    Route::post('account/update/address', 'ProfileController@upateAddress');

    //ratings and reviews
    Route::post('new/rating/{worker}', 'RatingController@store');
    Route::delete('remove/review/{rating}', 'RatingController@destroy');
    Route::post('update/review/{rating}', 'RatingController@updateReview');

    //contracts
    Route::get('contracts', 'ContractController@index');
    Route::post('contract/complete/{contract}', 'ContractController@completed');

    //contacts
    Route::post('contact-us', 'ContactController@contact');
});
=======
    Route::get('projects', 'ProjectController@index');
    Route::post('choose/proposal/{proposal}/{post}', 'ProposalController@choose');
    Route::get('admin/users','AdminController@users');
    Route::get('job_post','JobPostController@jobPost');
    Route::get('contracts','ContractController@Contracts');
});
>>>>>>> Stashed changes
