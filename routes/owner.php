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

Route::name('owner.')->group(function () {

    Route::middleware(['guest:owner'])->group(function () {
	    
	    Route::get('/', function () {
	       return view('auth.login', ['url' => 'owner']); 
	    })->name('login');

		Route::post('/', 'Auth\LoginController@ownerLogin')->name('login');
	
	});

	Route::middleware(['auth:owner'])->group(function () {
	    
		Route::get('/{any}', 'HomeController@ownerHome')->name('home');

		Route::get('/api/profile', 'ProfileController@showOwnerProfile')->name('profile');	
		Route::put('/profile', 'ProfileController@updateOwnerProfile')->name('profile');	
		Route::post('/password', 'ProfileController@updateOwnerPassword')->name('password');

		Route::get('/api/current-owner', 'OwnerController@currentOwner')->name('current-user');

		Route::get('/my-warehouses/{owner}/{perPage}', 'OwnerController@showOwnerAllWarehouses')->name('my-warehouses');
		Route::get('/search-warehouses/{owner}/{search}/{perPage}', 'OwnerController@searchOwnerAllWarehouses')->name('search-warehouses');

		Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
	
	});

});
