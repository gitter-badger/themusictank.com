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

// Artists
Route::get('artists', "ArtistController@index");
Route::get('artists/{slug}', "ArtistController@show");

// Albums
Route::get('albums/{slug}', "AlbumController@show");

// Tracks
Route::get('tracks/{slug}', "TrackController@show");

// Ajax
Route::get('ajax/bugreport', "AjaxController@bugreport");
Route::get('ajax/ytkey/{slug}', "AjaxController@ytkey");
Route::post('ajax/upvote/{type}', "AjaxController@upvote");

// Profiles
Route::get('profiles/dashboard', "ProfileController@dashboard")->middleware('auth');
Route::get('profiles/edit', "ProfileController@edit")->middleware('auth');;

Route::get('profiles/', "ProfileController@auth");
Route::get('profiles/login', "ProfileController@login");
Route::post('profiles/tmtlogin', "ProfileController@tmtlogin");
Route::get('profiles/facebook', "ProfileController@facebook");
Route::get('profiles/logout', "ProfileController@logout");
Route::get('profiles/create', "ProfileController@create");

// User areas
Route::get('tankers/{slug}', "ProfileController@show");


