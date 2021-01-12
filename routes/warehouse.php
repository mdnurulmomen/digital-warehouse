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

Route::name('warehouse.')->group(function () {

    Route::middleware(['guest:warehouse'])->group(function () {
	    
	    Route::get('/', function () {
	       return view('auth.login', ['url' => 'warehouse']); 
	    })->name('login');

		Route::post('/', 'Auth\LoginController@warehouseLogin')->name('login');
	
	});

	Route::middleware(['auth:warehouse'])->group(function () {
	    
		Route::get('/{any}', 'HomeController@warehouseHome')->name('home');	

		Route::get('/api/profile', 'ProfileController@showWarehouseProfile')->name('profile');	
		Route::put('/warehouse-profile', 'ProfileController@updateWarehouseProfile')->name('profile');
		Route::put('/warehouse-deal', 'ProfileController@updateWarehouseDeal')->name('deal');
		Route::put('/warehouse-feature-previews', 'ProfileController@updateWarehouseFeaturesAndPreviews')->name('feature-previews');
		Route::put('/warehouse-storages', 'ProfileController@updateWarehouseStorages')->name('storages');
		Route::put('/warehouse-containers', 'ProfileController@updateWarehouseContainers')->name('containers');
		Route::post('/password', 'ProfileController@updateWarehousePassword')->name('password');

		Route::get('/api/rent-periods/{perPage?}', 'AssetController@showAllRentPeriods')->name('rent-periods');
		Route::get('/api/owners/{perPage?}', 'WarehouseController@showAllOwners')->name('warehouse-owners');
		Route::get('/api/storage-types/{perPage?}','AssetController@showAllStorageTypes')->name('storage-types');
		Route::get('/api/container-types/{perPage?}', 'AssetController@showAllContainers')->name('container-types');

		Route::get('/api/containers/{perPage?}', 'WarehouseController@showWarehouseAllContainers')->name('containers');
		Route::get('/api/search-containers/{search}/{perPage}', 'WarehouseController@searchWarehouseAllContainers')->name('search-containers');

		Route::get('/api/container-shelves/{container}/{perPage?}', 'WarehouseController@showContainerAllShelves')->name('container-shelves');
		Route::get('/api/search-container-shelves/{container}/{search}/{perPage}', 'WarehouseController@searchContainerAllShelves')->name('search-container-shelves');

		Route::get('/api/shelf-units/{shelf}/{perPage?}', 'WarehouseController@showShelfAllUnits')->name('shelf-units');
		Route::get('/api/search-shelf-units/{shelf}/{search}/{perPage}', 'WarehouseController@searchShelfAllUnits')->name('search-shelf-units');

		Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

		Route::fallback(function () {
		    
			return view('layouts.warehouse');
		    
		});
	
	});

});
