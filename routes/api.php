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
Route::group(['prefix' => 'v1/auth/'], function() {
    Route::post('login', 'App\Http\Controllers\api\v1\AuthController@login');
    Route::post('register', 'App\Http\Controllers\api\v1\AuthController@register');
    Route::post('reset-passowrd-submit','App\Http\Controllers\api\v1\ResetPasswordController@resetPassword' );
    Route::post('confirm-reset','App\Http\Controllers\api\v1\ResetPasswordController@confirmReset' );
    Route::get('login/{provider}', 'App\Http\Controllers\api\v1\AuthController@redirectToProvider')->middleware('web');
    Route::get('{provider}/callback', 'App\Http\Controllers\api\v1\AuthController@handleProviderCallback')->middleware('web');


    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('logout', 'App\Http\Controllers\api\v1\AuthController@logout');
    });



    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });
});

