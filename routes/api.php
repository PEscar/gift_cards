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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {

    Route::put('/password', 'Api\UserController@updatePassword')->name('password.change');
    Route::post('/ventas', 'Api\VentaController@store')->name('api.ventas.create');

    Route::put('/configuracion/update', 'Api\ConfigController@update')->name('config.update');

    Route::prefix('users')->group(function () {
        Route::get('/', 'Api\UserController@index')->name('api.users.index');
        Route::get('/{id}', 'Api\UserController@show')->name('api.users.show');
        Route::delete('/{id}', 'Api\UserController@destroy')->name('api.users.destroy');
        Route::put('/update/{id}', 'Api\UserController@update')->name('api.users.update');
        Route::post('/create', 'Api\UserController@store')->name('api.users.create');
    });

    Route::prefix('giftcards')->group(function () {
        Route::get('/', 'Api\GiftCardController@index')->name('api.giftcards.index');
        Route::post('/asignar/{codigo?}', 'Api\GiftCardController@asignar')->name('api.giftcards.asignar');
        Route::get('/validar/{codigo?}', 'Api\GiftCardController@validar')->name('api.giftcards.validar');
    });
});