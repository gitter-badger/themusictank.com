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

// Maintenance routes almost considered hardcoded for the CLI
Route::post('/maintenance/discog/artists/update', "Api\Maintenance\ArtistMaintenanceController@sync");
Route::post('/maintenance/discog/artists/add', "Api\Maintenance\ArtistMaintenanceController@add");
Route::post('/maintenance/discog/artists/sync-snapshot', "Api\Maintenance\ArtistMaintenanceController@syncRequired");
Route::get('/maintenance/discog/artists/thumbnails', "Api\Maintenance\ArtistMaintenanceController@missingThumbnails");

Route::post('/maintenance/discog/albums/update', "Api\Maintenance\AlbumMaintenanceController@sync");
Route::post('/maintenance/discog/albums/sync-snapshot', "Api\Maintenance\AlbumMaintenanceController@syncRequired");
Route::get('/maintenance/discog/albums/thumbnails', "Api\Maintenance\AlbumMaintenanceController@missingThumbnails");

Route::post('/maintenance/discog/track/update', "Api\AlbumMaintenanceController@sync");
Route::post('/maintenance/discog/track/sync-snapshot', "Api\Maintenance\AlbumMaintenanceController@syncRequired");

// General routes
Route::resource('configurations', 'Api\ConfigurationController');
Route::resource('artists', 'Api\ArtistController');
Route::resource('albums', 'Api\AlbumController');
Route::resource('tracks', 'Api\TrackController');

