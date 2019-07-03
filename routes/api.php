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

Route::group(['prefix' => '/v1/users'],function (){

    Route::post('/login','User\UserController@wxLogin');
    Route::post('/address/add','User\UserController@wxLogin');
});

Route::group(['prefix' => '/v1/products'],function (){
    //favorite
    Route::get('/{product}/favorite','product\ProductsController@favor');
    Route::get('/favorites','product\ProductsController@favorites');
    //
    Route::get('','Product\ProductsController@index');
    Route::get('/{product}','Product\ProductsController@show');
});
//Cart;
Route::group(['prefix' => '/V1/cart'],function (){
    //add
    Route::post('/add','Cart\CartController@add');

});
