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

	Route::prefix('giftcards')->group(function () {

		Route::get('/show_random_qr', 'GiftCardController@show_random_qr')->name('giftcards.show_random_qr');
		Route::get('/validar/{codigo?}', 'GiftCardController@show')->name('giftcards.show');
		Route::get('/minoristas', 'GiftCardController@indexMinoristas')->name('giftcards.index.minoristas');
		Route::get('/mayoristas', 'GiftCardController@indexMayoristas')->name('giftcards.index.mayoristas');
	});

	Route::get('/usuarios', 'UserController@index')->name('usuarios.index');
	Route::get('/password', 'UserController@showUpdatePasswordView')->name('password.update.show');
	Route::get('/configuracion', 'UserController@showConfiguracionView')->name('configuracion.update.show');

	Route::get('/productos', 'ProductoController@index')->name('productos.index');
	Route::get('/empresas', 'EmpresaController@index')->name('empresas.index');
	Route::get('/ventas', 'VentaController@index')->name('ventas.index');
});

Route::prefix('giftcards')->group(function () {

	Route::any('/create', 'Api\VentaController@importOrderFromTiendaNube')->name('tiendanube.orders.create');
	Route::any('/update', 'Api\VentaController@updateOrderFromTiendaNube')->name('tiendanube.orders.update');
	//Route::get('/test_pdf_download', 'Api\VentaController@test_pdf_download')->name('tiendanube.orders.test_pdf_download');
});

Route::prefix('vouchers')->group(function () {

	Route::get('/download/{id}', 'VentaController@downloadVoucher')->name('voucher.download');

});