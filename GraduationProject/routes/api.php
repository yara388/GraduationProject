<?php

use App\Http\Controllers\API\ActionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\VideoController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\PlaylistController;
use App\Http\Controllers\API\ReplyController;
use App\Http\Controllers\API\SubscriberController;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    // Authentication and Profile Routes
    Route::post('login', [AuthController::class,'login']);
    Route::post('register', [AuthController::class,'register']);
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::post('profile', [AuthController::class,'me']);
    Route::post('update', [AuthController::class,'update']);
    Route::post('delete', [AuthController::class,'delete']);
    // Home Route
    Route::get('/',[HomeController::class,'index']);
    // Videos Routes
    // Route::get('videos',[VideoController::class,'index']);
    Route::post('store-video', [VideoController::class,'store']);
    Route::post('edit-video/{id?}', [VideoController::class,'edit']);
    Route::post('delete-video/{id?}', [VideoController::class,'delete']);
    Route::get('list-videos', [VideoController::class,'myVideos']);
    Route::get('show-video/{id?}', [VideoController::class,'show']);
    // Comments Routes
    Route::post('store-comment/{id?}', [CommentController::class,'store']);
    Route::post('update-comment/{id?}/{comment_id?}', [CommentController::class,'update']);
    Route::post('delete-comment/{id?}/{comment_id?}', [CommentController::class,'delete']);
    // Subscriptions Routes
    Route::post('subscribe', [SubscriberController::class,'subscribe']);
    Route::get('unsubscribe/{user_id?}', [SubscriberController::class,'unsubscribe']);
    // Playlist Routes
    Route::get('/playlists', [PlaylistController::class,'index']);
    Route::get('/list-playlists', [PlaylistController::class,'myPlaylist']);
    Route::get('/playlist/{id?}', [PlaylistController::class,'show']);
    Route::post('/store-playlist', [PlaylistController::class,'store']);
    Route::post('/update-playlist/{id?}', [PlaylistController::class,'update']);
    Route::get('/delete-playlist/{id?}', [PlaylistController::class,'delete']);
    // Playlist Video Routes
    Route::post('/add-video-to-playlist/{id?}', [PlaylistController::class,'addVideoToPlaylist']);
    Route::post('/remove-video-from-playlist/{playlist_id?}/{video_id?}', [PlaylistController::class,'removeVideoFromPlaylist']);
    // Action Routes
    Route::get('/like/{id?}',[ActionController::class,'like']);
    Route::get('/dislike/{id?}',[ActionController::class,'dislike']);
    // Replies Routes
    Route::post('store-reply/{comment_id?}/{video_id?}',[ReplyController::class,'store']);
    Route::post('update-reply/{comment_id?}/{video_id?}/{id?}',[ReplyController::class,'update']);
    Route::post('delete-reply/{comment_id?}/{video_id?}/{id?}',[ReplyController::class,'delete']);
});
