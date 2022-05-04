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

//  user url

Route::group(['middleware' => ['auth:api']], function () {
    //  user url
    Route::get('users-with-comments', 'UserController@getUsersWithComments');
    Route::group(['prefix' => 'users'], function () {
        Route::post('', 'UserController@saveUser');
        Route::get('', 'UserController@getUsers');
        Route::put('{id}','UserController@editUser');
        Route::get('{id}','UserController@getUserById');
    });

});

Route::get('err', function (Request $request) {
    return response(['data' => 'Access denied'], 401);
})->name('err');
