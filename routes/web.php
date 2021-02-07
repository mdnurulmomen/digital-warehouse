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
    return view('welcome');
})->name('website');

Auth::routes();

Route::name('merchant.')->group(function () {

	Route::middleware(['auth:merchant'])->group(function () {

		Route::get('/{any}', 'HomeController@index')->name('home');

		Route::get('/api/profile', 'ProfileController@showMerchantProfile')->name('profile');	
		Route::put('/profile', 'ProfileController@updateMerchantProfile')->name('profile');	
		Route::post('/password', 'ProfileController@updateMerchantPassword')->name('password');

		// Route::get('/api/current-user', 'MerchantController@currentMerchant')->name('current-user');
		
		Route::get('/api/products/{perPage?}', 'MerchantController@showMerchantAllProducts')->name('products');
		Route::get('/search-products/{query}/{perPage}', 'MerchantController@searchMerchantAllProducts')->name('products');

		Route::get('/api/requisitions/{perPage?}', 'MerchantController@showMerchantAllRequisitions')->name('requisitions');
		Route::post('/requisitions/{perPage}', 'MerchantController@makeNewRequisition')->name('requisitions');
		Route::get('/api/search-requisitions/{search}/{perPage?}', 'MerchantController@searchMerchantAllRequisitions')->name('search-requisitions');

		Route::post('/receive-dispatched-products/{perPage}', 'MerchantController@receiveDispatchedProducts')->name('receive-dispatched-products');

	});

});