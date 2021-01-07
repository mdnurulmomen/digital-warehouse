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

Route::name('warhouse.')->group(function () {

    Route::middleware(['guest:warhouse'])->group(function () {
	    
	    Route::get('/', function () {
	       return view('auth.login', ['url' => 'warhouse']); 
	    })->name('login');

		Route::post('/', 'Auth\LoginController@warhouseLogin')->name('login');
	
	});

	Route::middleware(['auth:warhouse'])->group(function () {
	    
		Route::get('/{any}', 'HomeController@warhouseHome')->name('home');	

		Route::get('/api/profile', 'ProfileController@showWarhouseProfile')->name('profile');	
		Route::put('/warhouse-profile', 'ProfileController@updateWarhouseProfile')->name('profile');
		Route::put('/warhouse-deal', 'ProfileController@updateWarhouseDeal')->name('deal');		
		Route::post('/password', 'ProfileController@updateWarhousePassword')->name('password');

		Route::get('/api/rent-periods/{perPage?}', 'AssetController@showAllRentPeriods')->name('rent-periods');
		Route::get('/api/owners/{perPage?}', 'WarhouseController@showAllOwners')->name('warhouse-owners');
		Route::get('/api/storage-types/{perPage?}','AssetController@showAllStorageTypes')->name('storage-types');
		Route::get('/api/containers/{perPage?}', 'AssetController@showAllContainers')->name('containers');

		Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
	
	});

});
