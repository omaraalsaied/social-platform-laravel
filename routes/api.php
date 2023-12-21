<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\AuthController;
use Illuminate\Support\Facades\Auth;

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
Route::group(['prefix' => 'v1/users/'], function() {
    Route::post('login', 'App\Http\Controllers\api\v1\AuthController@login');
    Route::post('register', 'App\Http\Controllers\api\v1\AuthController@register');
    
    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('logout', 'App\Http\Controllers\api\v1\AuthController@logout');
    });



    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });
});

