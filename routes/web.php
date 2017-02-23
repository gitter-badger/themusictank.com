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

Route::get("/", "PageController@home");
Route::get("/about", "PageController@about");
Route::get("/legal", "PageController@legal");

Route::get('artists/{slug}', "ArtistController@show");
Route::get('albums/{slug}', "AlbumController@show");
Route::get('tracks/{slug}', "TrackController@show");

Route::get('ajax/bugreport', "AjaxController@bugreport");
