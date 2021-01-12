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

Route::name('manager.')->group(function () {

    Route::middleware(['guest:manager'])->group(function () {
	    
	    Route::get('/', function () {
	       return view('auth.login', ['url' => 'manager']); 
	    })->name('login');

		Route::post('/', 'Auth\LoginController@managerLogin')->name('login');

	});

	Route::middleware(['auth:manager'])->group(function () {
	    
		Route::get('/{any}', 'HomeController@managerHome')->name('home');

		Route::get('/api/profile', 'ProfileController@showManagerProfile')->name('profile');	
		Route::put('/profile', 'ProfileController@updateManagerProfile')->name('profile');	
		Route::post('/password', 'ProfileController@updateManagerPassword')->name('password');

		Route::get('/my-warehouses/{perPage}', 'ManagerController@showManagerAllWarehouses')->name('my-warehouses');
		Route::get('/search-warehouses/{search}/{perPage}', 'ManagerController@searchManagerAllWarehouses')->name('search-warehouses');

		Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

		/*
		
		Route::get('/api/warehouses/{perPage}', 'WarehouseController@showAllWarehouses')->name('warehouses');
		Route::post('/warehouses/{perPage}', 'WarehouseController@storeNewWarehouse')->name('warehouses');	
		Route::put('/warehouses/{warehouse}/{perPage}', 'WarehouseController@updateWarehouse')->name('warehouses');	
		Route::delete('/warehouses/{warehouse}/{perPage}', 'WarehouseController@deleteWarehouse')->name('warehouses');	
		Route::patch('/warehouses/{warehouse}/{perPage}', 'WarehouseController@restoreWarehouse')->name('warehouses');
		Route::get('/api/search-warehouses/{search}/{perPage}', 'WarehouseController@searchAllWarehouses')->name('search-warehouses');

		Route::get('/api/products/{perPage}', 'ProductController@showAllProducts')->name('products');
		Route::post('/products/{perPage}', 'ProductController@storeNewProduct')->name('products');	
		Route::put('/products/{product}/{perPage}', 'ProductController@updateProduct')->name('products');
		Route::get('/api/search-products/{search}/{perPage}', 'ProductController@searchAllProducts')->name('search-products');

		 */
	
	});

});
