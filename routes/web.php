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
Route::get("/", "PageController@home")->name('home');
Route::get("/about", "PageController@about")->name('about');
Route::get("/legal", "PageController@legal")->name('legal');

// Achievements
Route::get('achievements', "AchievementController@index")->name('achievements');
Route::get('achievements/list', "AchievementController@all")->name('achievement-list');
Route::get('achievements/{slug}', "AchievementController@show")->name('achievement');


// Artists
Route::get('artists', "ArtistController@index")->name('artists');
Route::get('artists/{slug}', "ArtistController@show")->name('artist');

// Albums
Route::get('albums/{slug}', "AlbumController@show")->name('album');

// Tracks
Route::get('tracks/{slug}', "TrackController@show")->name('track');
Route::get('tracks/{slug}/review', "TrackController@review")->middleware('auth')->name('review');

// Ajax
Route::get('ajax/search/artist', "Ajax\SearchController@artist")->name('search-artists');
Route::get('ajax/search/album', "Ajax\SearchController@album")->name('search-albums');
Route::get('ajax/search/track', "Ajax\SearchController@track")->name('search-tracks');
Route::get('ajax/search/user', "Ajax\SearchController@user")->name('search-users');

Route::post('ajax/upvote/track/add', "Ajax\UpvoteController@addTrack")->middleware('auth')->name('upvote-track');
Route::post('ajax/upvote/track/remove', "Ajax\UpvoteController@removeTrack")->middleware('auth')->name('deupvote-track');
Route::post('ajax/upvote/album/add', "Ajax\UpvoteController@addAlbum")->middleware('auth')->name('upvote-album');
Route::post('ajax/upvote/album/remove', "Ajax\UpvoteController@removeAlbum")->middleware('auth')->name('deupvote-album');

Route::post('ajax/tanker/whats-up', "Ajax\UserController@whatsUp")->middleware('auth')->name('activity-ping');
Route::post('ajax/tanker/ok-stfu', "Ajax\UserController@okstfu")->middleware('auth')->name('activity-mark-as-read');
Route::post('ajax/tanker/follow', "Ajax\UserController@follow")->middleware('auth')->name('follow-user');
Route::post('ajax/tanker/unfollow', "Ajax\UserController@unfollow")->middleware('auth')->name('unfollow-user');
Route::post('ajax/tanker/bugreport', "Ajax\UserController@bugreport")->name('bug-report');

Route::post('ajax/track/ytkey/{slug}', "Ajax\TrackController@ytkey")->name('get-toutube-key');
Route::post('ajax/track/{slug}/saveCurvePart', "Ajax\TrackController@saveCurvePart")->middleware('auth')->name('save-partial-curve');
Route::post('ajax/track/{slug}/getNext', "Ajax\TrackController@getNextTrack")->middleware('auth')->name('next-track');

// auth landing
Route::get('profiles/auth/', "Auth\AuthController@index")->name('login');
Route::get('profiles/auth/logout', "Auth\AuthController@logout")->name('logout');

// -> facebook
Route::get('profiles/auth/facebook/redirect', 'Auth\Social\FacebookController@redirect')->name('facebook-login');
Route::get('profiles/auth/facebook/callback', 'Auth\Social\FacebookController@callback');

// -> tmt accounts
Route::get('profiles/auth/tmt/login', "Auth\Tmt\LoginController@showLoginForm")->name('tmt-login');
Route::get('profiles/auth/tmt/register', "Auth\Tmt\RegisterController@showRegisterForm")->name('tmt-register');

Route::post('profiles/auth/tmt/login/attempt', "Auth\Tmt\LoginController@login")->name('tmt-login-do');
Route::post('profiles/auth/tmt/register/attempt', "Auth\Tmt\RegisterController@register")->name('tmt-register-do');

// -> account pages
Route::get('you', "Profile\DashboardController@index")->middleware('auth')->name('dashboard');
Route::get('you/notifications', "NotificationController@index")->middleware('auth')->name('notifications');

Route::get('you/edit', "Profile\ManageController@edit")->middleware('auth')->name('profile');
Route::get('you/edit/thirdparty', "Profile\ManageController@thirdparty")->middleware('auth')->name('profile-thirdparty');
Route::get('you/edit/password', "Profile\ManageController@password")->middleware('auth')->name('profile-password');
Route::get('you/edit/api', "Profile\ManageController@api")->middleware('auth')->name('profile-api');
Route::get('you/edit/delete', "Profile\ManageController@delete")->middleware('auth')->name('profile-delete');

Route::post('you/edit/general/update', "Profile\ManageController@saveGeneral")->middleware('auth')->name('profile-save');
Route::post('you/edit/thirdparty/revoke', "Profile\ManageController@revokeThirdParty")->middleware('auth')->name('profile-thirdparty-revoke');
Route::post('you/edit/password/update', "Profile\ManageController@savePassword")->middleware('auth')->name('profile-password-save');
Route::post('you/edit/delete/attempt', "Profile\ManageController@saveDelete")->middleware('auth')->name('profile-delete-save');

// User areas
Route::get('tankers/{slug}', "UserController@show")->name('user');
Route::get('tankers/{slug}/curve/{trackSlug}', "TrackController@viewUserReview")->name('user-review');
Route::get('tankers/{slug}/achievements', "UserController@achievements")->name('user-achievements');

// Others
Route::get('admin/console', "AdminController@console")->middleware('auth')->name('admin');
Route::post('admin/reset-review-cache', "AdminController@resetReviewCache")->middleware('auth')->name('admin-resetcache');
