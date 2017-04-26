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
Route::get('tracks/{slug}/review', "TrackController@review")->middleware('auth');
Route::get('tracks/{slug}', "TrackController@show");

// Ajax
Route::get('ajax/bugreport', "AjaxController@bugreport");
Route::get('ajax/ytkey/{slug}', "AjaxController@ytkey");
Route::get('ajax/artistSearch', "AjaxController@artistSearch");
Route::get('ajax/albumSearch', "AjaxController@albumSearch");
Route::get('ajax/trackSearch', "AjaxController@trackSearch");

Route::post('ajax/addTrackUpvote', "AjaxController@addTrackUpvote")->middleware('auth');
Route::post('ajax/addAlbumUpvote', "AjaxController@addAlbumUpvote")->middleware('auth');
Route::post('ajax/removeTrackUpvote', "AjaxController@removeTrackUpvote")->middleware('auth');
Route::post('ajax/removeAlbumUpvote', "AjaxController@removeAlbumUpvote")->middleware('auth');
Route::get('ajax/whatsUp', "AjaxController@whatsUp")->middleware('auth');
Route::get('ajax/okstfu', "AjaxController@okstfu")->middleware('auth');

Route::post('ajax/{slug}/saveCurvePart', "AjaxController@saveCurvePart")->middleware('auth');
Route::post('ajax/{slug}/getNext', "AjaxController@getNextTrack")->middleware('auth');

// Profiles
Route::get('you', "ProfileController@dashboard")->middleware('auth');
Route::get('you/edit', "ProfileController@edit")->middleware('auth');
Route::get('you/notifications', "NotificationController@index")->middleware('auth');

Route::get('profiles/', "ProfileController@auth");
Route::get('profiles/login', "ProfileController@login");
Route::get('profiles/logout', "Auth\LoginController@logout");
Route::get('profiles/facebook', "ProfileController@facebook");
Route::get('profiles/create', "ProfileController@create");

Route::post('profiles/login', "Auth\LoginController@login");
Route::post('profiles/create', "ProfileController@create");

// User areas
Route::get('tankers/{slug}', "ProfileController@show");
Route::get('tankers/{slug}/curve/{trackSlug}', "ProfileController@showCurve");


// Others
Route::get('admin/console', "AdminController@console")->middleware('auth');
