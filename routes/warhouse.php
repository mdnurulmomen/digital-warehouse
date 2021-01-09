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
		Route::put('/warhouse-feature-previews', 'ProfileController@updateWarhouseFeaturesAndPreviews')->name('feature-previews');
		Route::put('/warhouse-storages', 'ProfileController@updateWarhouseStorages')->name('storages');
		Route::put('/warhouse-containers', 'ProfileController@updateWarhouseContainers')->name('containers');
		Route::post('/password', 'ProfileController@updateWarhousePassword')->name('password');

		Route::get('/api/rent-periods/{perPage?}', 'AssetController@showAllRentPeriods')->name('rent-periods');
		Route::get('/api/owners/{perPage?}', 'WarhouseController@showAllOwners')->name('warhouse-owners');
		Route::get('/api/storage-types/{perPage?}','AssetController@showAllStorageTypes')->name('storage-types');
		Route::get('/api/container-types/{perPage?}', 'AssetController@showAllContainers')->name('container-types');

		Route::get('/api/containers/{perPage?}', 'WarhouseController@showWarhouseAllContainers')->name('containers');
		Route::get('/api/search-containers/{search}/{perPage}', 'WarhouseController@searchWarhouseAllContainers')->name('containers');

		Route::get('/api/container-shelves/{container}/{perPage?}', 'WarhouseController@showContainerAllShelves')->name('container-shelves');
		Route::get('/api/search-container-shelves/{container}/{search}/{perPage}', 'WarhouseController@searchContainerAllShelves')->name('search-container-shelves');

		Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
	
	});

});
