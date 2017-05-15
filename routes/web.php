<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

// Internals
Route::get("/", "PageController@home");
Route::get("/about", "PageController@about");
Route::get("/legal", "PageController@legal");
Route::get("/api/is-down", "PageController@apiIsDown");
Route::get("/api/error", "PageController@apiError");

// Artists
Route::get('artists', "ArtistController@index");
Route::get('artists/{slug}', "ArtistController@show");

// Albums
Route::get('albums/{slug}', "AlbumController@show");

// Tracks
Route::get('tracks/{slug}', "TrackController@show");
Route::get('tracks/{slug}/review', "TrackController@review")->middleware('auth');

// Ajax

Route::get('ajax/search/artist', "Ajax\SearchController@artist");
Route::get('ajax/search/album', "Ajax\SearchController@album");
Route::get('ajax/search/track', "Ajax\SearchController@track");
Route::get('ajax/search/user', "Ajax\SearchController@user");

Route::post('ajax/upvote/track/add', "Ajax\UpvoteController@addTrack")->middleware('auth');
Route::post('ajax/upvote/track/remove', "Ajax\UpvoteController@removeTrack")->middleware('auth');
Route::post('ajax/upvote/album/add', "Ajax\UpvoteController@addAlbum")->middleware('auth');
Route::post('ajax/upvote/album/remove', "Ajax\UpvoteController@removeAlbum")->middleware('auth');

Route::post('ajax/tanker/whats-up', "Ajax\UserController@whatsUp")->middleware('auth');
Route::post('ajax/tanker/ok-stfu', "Ajax\UserController@okstfu")->middleware('auth');
Route::post('ajax/tanker/bugreport', "Ajax\UserController@bugreport");

Route::post('ajax/track/ytkey/{slug}', "Ajax\TrackController@ytkey");
Route::post('ajax/track/{slug}/saveCurvePart', "Ajax\TrackController@saveCurvePart")->middleware('auth');
Route::post('ajax/track/{slug}/getNext', "Ajax\TrackController@getNextTrack")->middleware('auth');


// auth landing
Route::get('profiles/auth/', "Auth\AuthController@index");
Route::get('profiles/auth/logout', "Auth\AuthController@logout");

// -> facebook
Route::get('profiles/auth/facebook/redirect', 'Auth\SocialController@facebookRedirect');
Route::get('profiles/auth/facebook/callback', 'Auth\SocialController@facebookCallback');

// -> tmt accounts
Route::post('profiles/auth/tmt/login', "Auth\TmtController@login");
Route::get('profiles/auth/tmt/create', "Auth\TmtController@create");
Route::post('profiles/auth/tmt/save', "Auth\TmtController@save");

// -> account pages
Route::get('you', "UserController@dashboard")->middleware('auth');
Route::get('you/edit', "UserController@edit")->middleware('auth');
Route::post('you/save', "UserController@save")->middleware('auth');
Route::get('you/notifications', "NotificationController@index")->middleware('auth');

// User areas
Route::get('tankers/{slug}', "UserController@show");
Route::get('tankers/{slug}/curve/{trackSlug}', "UserController@showCurve");


// Others
Route::get('admin/console', "AdminController@console")->middleware('auth');
