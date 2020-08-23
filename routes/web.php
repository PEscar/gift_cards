<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {

	if ( auth()->user() )
	{
		return redirect('home');
	} else {
		return view('auth.login');
	}
});

Auth::routes();
Route::middleware('auth:web')->group(function () {

	Route::get('/home', 'HomeController@index')->name('home');
	Route::get('/giftcards/show_random_qr', 'GiftCardController@show_random_qr')->name('giftcards.show_random_qr');
	Route::get('/giftcards/{codigo?}', 'GiftCardController@show')->name('giftcards.show');

	Route::get('/usuarios', 'UserController@index')->name('usuarios.index');
});