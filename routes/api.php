<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', 'App\Http\Controllers\UserController@login');
Route::post('register', 'App\Http\Controllers\UserController@register');
Route::get('test2', 'App\Http\Controllers\TestController@test2');


Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::get('test', 'App\Http\Controllers\UserController@test');
    Route::post('logout', 'App\Http\Controllers\UserController@logout');

    // Route::get('test2', 'App\Http\Controllers\TestController@test2');
    Route::get('userdatatest', 'App\Http\Controllers\TestController@userdatatest');

    Route::apiResource('checkin', App\Http\Controllers\CheckInController::class);
    Route::apiResource('checkout', App\Http\Controllers\CheckOutController::class);
    Route::get('statuscheck', 'App\Http\Controllers\ChecksController@statuscheck');
});