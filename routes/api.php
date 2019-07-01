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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => '/v1'],function (){

    Route::post('/user/login','User\UserController@wxLogin');
    Route::post('/user/address/add','User\UserAddressesController@store');
    Route::post('/user/address/{UserAddress}/update','User\UserAddressesController@update');
    Route::get('/user/address/{UserAddress}/restore','User\UserAddressesController@restore');
});
