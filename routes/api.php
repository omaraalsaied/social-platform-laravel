<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::group(['prefix' => 'v1/'], function () {
    Route::group(['prefix' => 'auth/'], function () {
        Route::post('login', 'App\Http\Controllers\api\v1\AuthController@login');
        Route::post('register', 'App\Http\Controllers\api\v1\AuthController@register');
        Route::post('reset-passowrd-submit', 'App\Http\Controllers\api\v1\ResetPasswordController@resetPassword');
        Route::post('confirm-reset', 'App\Http\Controllers\api\v1\ResetPasswordController@confirmReset');
        Route::get('login/{provider}', 'App\Http\Controllers\api\v1\AuthController@redirectToProvider')->middleware('web');
        Route::get('{provider}/callback', 'App\Http\Controllers\api\v1\AuthController@handleProviderCallback')->middleware('web');
        Route::post('logout', 'App\Http\Controllers\api\v1\AuthController@logout')->middleware('auth:api');
    });


    Route::group(['middleware' => ['auth:api']], function () {
        Route::group(['prefix' => 'users/'], function () {
            Route::get('{id}', 'App\Http\Controllers\api\v1\UserController@view');
            Route::post('search','App\Http\Controllers\api\v1\UserController@search');
            Route::patch('update/{id}', 'App\Http\Controllers\api\v1\UserController@update');
        });
        Route::get('/profile', 'App\Http\Controllers\api\v1\UserController@profile');
        
        Route::group(['prefix' => 'posts'], function () {
            Route::get('/','App\Http\Controllers\api\v1\PostController@index');
            Route::get('/{id}','App\Http\Controllers\api\v1\PostController@show');
            Route::post('store','App\Http\Controllers\api\v1\PostController@store');
            Route::patch('update/{id}','App\Http\Controllers\api\v1\PostController@update');
            Route::delete('destroy/{id}','App\Http\Controllers\api\v1\PostController@destroy');
            Route::post('interact/{id}', 'App\Http\Controllers\api\v1\PostController@userInteraction');
            Route::get('likers/{id}', 'App\Http\Controllers\api\v1\PostController@getLikers');
            Route::post('share/{id}', 'App\Http\Controllers\api\v1\PostController@share');
        });

        Route::group(['prefix' => 'comments'], function () {
            Route::get('/{id}','App\Http\Controllers\api\v1\CommentController@show');
            Route::post('store/{post_id}','App\Http\Controllers\api\v1\CommentController@store');
            Route::patch('update/{id}','App\Http\Controllers\api\v1\CommentController@update');
            Route::delete('destroy/{id}','App\Http\Controllers\api\v1\CommentController@destroy');
            Route::post('interact/{id}', 'App\Http\Controllers\api\v1\CommentController@userInteraction');
            Route::get('likers/{id}', 'App\Http\Controllers\api\v1\CommentController@getLikers');

        });

        Route::group(['prefix' => 'friendships'], function () {
            Route::get('pending-requests','App\Http\Controllers\api\v1\UserController@getFriendRequests');
            Route::post('send-request/{id}','App\Http\Controllers\api\v1\UserController@sendFriendRequest');
            Route::post('respond-to-request/{id}','App\Http\Controllers\api\v1\UserController@RespondToFriendRequest');
            Route::get('list','App\Http\Controllers\api\v1\UserController@listFriends');
            Route::post('manage/{id}','App\Http\Controllers\api\v1\UserController@manageFriend');
        });

    });





});
