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

		// profile
		Route::get('/api/profile', 'ProfileController@showWarehouseProfile')->name('profile');	
		Route::put('/warehouse-profile', 'ProfileController@updateWarehouseProfile')->name('profile');
		Route::put('/warehouse-deal', 'ProfileController@updateWarehouseDeal')->name('deal');
		Route::put('/warehouse-feature-previews', 'ProfileController@updateWarehouseFeaturesAndPreviews')->name('feature-previews');
		Route::put('/warehouse-storages', 'ProfileController@updateWarehouseStorages')->name('storages');
		Route::put('/warehouse-containers', 'ProfileController@updateWarehouseContainers')->name('containers');
		Route::post('/password', 'ProfileController@updateWarehousePassword')->name('password');

		/* complementary routes for warehouse (my-properties) */
		Route::get('/api/rent-periods/{perPage?}', 'AssetController@showAllRentPeriods')->name('rent-periods');
		Route::get('/api/owners/{perPage?}', 'WarehouseController@showAllOwners')->name('warehouse-owners');
		Route::get('/api/storage-types/{perPage?}','AssetController@showAllStorageTypes')->name('storage-types');
		Route::get('/api/containers/{perPage?}', 'AssetController@showAllContainers')->name('container-types');

		// my-properties
		Route::get('/api/my-containers/{perPage?}', 'WarehouseController@showMyContainers')->name('my-containers');
		Route::get('/api/search-my-containers/{search}/{perPage}', 'WarehouseController@searchMyAllContainers')->name('search-my-containers');

		Route::get('/api/my-container-shelves/{container}/{perPage?}', 'WarehouseController@showMyContainerAllShelves')->name('my-container-shelves');
		Route::get('/api/search-my-container-shelves/{container}/{search}/{perPage}', 'WarehouseController@searchMyContainerAllShelves')->name('search-my-container-shelves');

		Route::get('/api/my-shelf-units/{shelf}/{perPage?}', 'WarehouseController@showMyShelfAllUnits')->name('my-shelf-units');
		Route::get('/api/search-my-shelf-units/{shelf}/{search}/{perPage}', 'WarehouseController@searchMyShelfAllUnits')->name('search-my-shelf-units');

		// my-products
		Route::get('/my-products/{perPage}', 'ProductController@showManagerAllProducts')->name('my-products');
		Route::get('/search-my-products/{search}/{perPage}', 'ProductController@searchManagerAllProducts')->name('search-my-products');

		/* special routes */
		/**/
		
		Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

		Route::fallback(function () {

			$access_token = Illuminate\Support\Str::random(60);
        	// $roles = \Auth::guard('warehouse')->user()->roles;
        	// $permissions = Auth::guard('warehouse')->user()->permissions;
        	$generalSettings = \App\Models\ApplicationSetting::firstOrCreate([]);
			// return view('layouts.warehouse', ['permissions' => $permissions, 'roles' => $roles, 'access_token' => $access_token, 'general_settings' => $generalSettings]);
			return view('layouts.warehouse', ['access_token' => $access_token, 'general_settings' => $generalSettings]);
		    
		});
	
	});

});
