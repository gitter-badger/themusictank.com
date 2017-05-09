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
Route::get('ajax/bugreport', "AjaxController@bugreport");
Route::get('ajax/ytkey/{slug}', "AjaxController@ytkey");
Route::get('ajax/artistSearch', "AjaxController@artistSearch");
Route::get('ajax/albumSearch', "AjaxController@albumSearch");
Route::get('ajax/trackSearch', "AjaxController@trackSearch");
Route::get('ajax/userSearch', "AjaxController@userSearch");
Route::get('ajax/whatsUp', "AjaxController@whatsUp")->middleware('auth');
Route::get('ajax/okstfu', "AjaxController@okstfu")->middleware('auth');
Route::post('ajax/addTrackUpvote', "AjaxController@addTrackUpvote")->middleware('auth');
Route::post('ajax/addAlbumUpvote', "AjaxController@addAlbumUpvote")->middleware('auth');
Route::post('ajax/removeTrackUpvote', "AjaxController@removeTrackUpvote")->middleware('auth');
Route::post('ajax/removeAlbumUpvote', "AjaxController@removeAlbumUpvote")->middleware('auth');

Route::post('ajax/{slug}/saveCurvePart', "AjaxController@saveCurvePart")->middleware('auth');
Route::post('ajax/{slug}/getNext', "AjaxController@getNextTrack")->middleware('auth');

// Profiles

// auth landing
Route::get('profiles/auth/', "Auth\AuthController@index");
Route::get('profiles/auth/logout', "Auth\AuthController@logout");

// -> facebook
Route::get('profiles/auth/facebook/redirect', 'Auth\SocialController@facebookRedirect');
Route::get('profiles/auth/facebook/callback', 'Auth\SocialController@facebookCallback');

// -> tmt accounts
Route::post('profiles/auth/tmt/login', "Auth\TmtController@login");
Route::post('profiles/create', "UserController@create");

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
