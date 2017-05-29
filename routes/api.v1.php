<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/maintenance/discog/artists/update', "Api\MaintenanceController@artistSync");
Route::post('/maintenance/discog/artists/sync-snapshot', "Api\MaintenanceController@artistSyncRequired");
Route::post('/maintenance/discog/artists/existance-check', "Api\MaintenanceController@artistExistance");

Route::resource('configurations', 'Api\ConfigurationController');
Route::resource('artists', 'Api\ArtistController');
Route::resource('albums', 'Api\AlbumController');
Route::resource('tracks', 'Api\TrackController');

