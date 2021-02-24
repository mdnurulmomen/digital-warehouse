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

Route::name('admin.')->group(function () {
    
    Route::middleware(['guest:admin'])->group(function () {

	    Route::get('/', function () {
	       return view('auth.login', ['url' => 'admin']); 
	    })->name('login');

	    Route::post('/', 'Auth\LoginController@adminLogin')->name('login');

	});


	Route::middleware(['auth:admin'])->group(function () {
		
		Route::get('/{any}', 'HomeController@adminHome')->name('home');
		
		Route::get('/api/application-settings', 'SettingController@showApplicationSetting')->name('application-settings');
		Route::put('/payment-settings', 'SettingController@updatePaymentSetting')->name('payment-settings');
		Route::put('/contact-settings', 'SettingController@updateContactSetting')->name('contact-settings');	
		Route::put('/warehouse-settings', 'SettingController@updateWarehouseSetting')->name('warehouse-settings');	
		Route::put('/system-settings', 'SettingController@updateSystemSetting')->name('system-settings');

		Route::get('/api/profile', 'ProfileController@showAdminProfile')->name('profile');	
		Route::put('/profile', 'ProfileController@updateAdminProfile')->name('profile');	
		Route::post('/password', 'ProfileController@updateAdminPassword')->name('password');

		Route::get('/api/owners/{perPage?}', 'WarehouseController@showAllOwners')->name('warehouse-owners');
		Route::post('/owners/{perPage}', 'WarehouseController@storeNewOwner')->name('warehouse-owners');	
		Route::put('/owners/{owner}/{perPage}', 'WarehouseController@updateOwner')->name('warehouse-owners');	
		Route::delete('/owners/{owner}/{perPage}', 'WarehouseController@deleteOwner')->name('warehouse-owners');	
		Route::patch('/owners/{owner}/{perPage}', 'WarehouseController@restoreOwner')->name('warehouse-owners');
		Route::get('/api/search-owners/{search}/{perPage}', 'WarehouseController@searchAllOwners')->name('search-warehouse-owners');

		Route::get('/api/managers/{perPage}', 'ManagerController@showAllManagers')->name('managers');
		Route::post('/managers/{perPage}', 'ManagerController@storeNewManager')->name('managers');	
		Route::put('/managers/{manager}/{perPage}', 'ManagerController@updateManager')->name('managers');	
		Route::delete('/managers/{manager}/{perPage}', 'ManagerController@deleteManager')->name('managers');	
		Route::patch('/managers/{manager}/{perPage}', 'ManagerController@restoreManager')->name('managers');
		Route::get('/api/search-managers/{search}/{perPage}', 'ManagerController@searchAllManagers')->name('search-managers');

		Route::get('/api/merchants/{perPage?}', 'MerchantController@showAllMerchants')->name('merchants');
		Route::post('/merchants/{perPage}', 'MerchantController@storeNewMerchant')->name('merchants');	
		Route::put('/merchants/{merchant}/{perPage}', 'MerchantController@updateMerchant')->name('merchants');	
		Route::delete('/merchants/{merchant}/{perPage}', 'MerchantController@deleteMerchant')->name('merchants');
		Route::patch('/merchants/{merchant}/{perPage}', 'MerchantController@restoreMerchant')->name('merchants');
		Route::get('/api/search-merchants/{search}/{perPage}', 'MerchantController@searchAllMerchants')->name('search-merchants');

		Route::get('/api/storage-types/{perPage?}','AssetController@showAllStorageTypes')->name('storage-types');
		Route::post('/storage-types/{perPage}','AssetController@storeNewStorageType')->name('storage-types');	
		Route::put('/storage-types/{type}/{perPage}','AssetController@updateStorageType')->name('storage-types');	
		Route::delete('/storage-types/{type}/{perPage}','AssetController@deleteStorageType')->name('storage-types');	
		Route::patch('/storage-types/{type}/{perPage}','AssetController@restoreStorageType')->name('storage-types');
		Route::get('/api/search-storage-types/{search}/{perPage}','AssetController@searchAllStorageTypes')->name('search-storage-types');

		Route::get('/api/containers/{perPage?}', 'AssetController@showAllContainers')->name('containers');
		Route::post('/containers/{perPage}', 'AssetController@storeNewContainer')->name('containers');	
		Route::put('/containers/{container}/{perPage}', 'AssetController@updateContainer')->name('containers');	
		Route::delete('/containers/{container}/{perPage}', 'AssetController@deleteContainer')->name('containers');	
		Route::patch('/containers/{container}/{perPage}', 'AssetController@restoreContainer')->name('containers');
		Route::get('/api/search-containers/{search}/{perPage}', 'AssetController@searchAllContainers')->name('search-containers');

		Route::get('/api/warehouses/{perPage}', 'WarehouseController@showAllWarehouses')->name('warehouses');
		Route::post('/warehouses/{perPage}', 'WarehouseController@storeNewWarehouse')->name('warehouses');	
		Route::put('/warehouses/{warehouse}/{perPage}', 'WarehouseController@updateWarehouse')->name('warehouses');	
		Route::delete('/warehouses/{warehouse}/{perPage}', 'WarehouseController@deleteWarehouse')->name('warehouses');	
		Route::patch('/warehouses/{warehouse}/{perPage}', 'WarehouseController@restoreWarehouse')->name('warehouses');
		Route::get('/api/search-warehouses/{search}/{perPage}', 'WarehouseController@searchAllWarehouses')->name('search-warehouses');

		Route::get('/api/rent-periods/{perPage?}', 'AssetController@showAllRentPeriods')->name('rent-periods');
		Route::post('/rent-periods/{perPage}', 'AssetController@storeNewRentPeriod')->name('rent-periods');	
		Route::put('/rent-periods/{period}/{perPage}', 'AssetController@updateRentPeriod')->name('rent-periods');	
		Route::delete('/rent-periods/{period}/{perPage}', 'AssetController@deleteRentPeriod')->name('rent-periods');	
		Route::patch('/rent-periods/{period}/{perPage}', 'AssetController@restoreRentPeriod')->name('rent-periods');
		Route::get('/api/search-rent-periods/{search}/{perPage}', 'AssetController@searchAllRentPeriods')->name('search-rent-periods');

		Route::get('/api/variation-types/{perPage?}', 'AssetController@showAllVariationTypes')->name('variation-types');
		Route::post('/variation-types/{perPage}', 'AssetController@storeVariationType')->name('variation-types');	
		Route::put('/variation-types/{warehouse}/{perPage}', 'AssetController@updateVariationType')->name('variation-types');	
		Route::delete('/variation-types/{warehouse}/{perPage}', 'AssetController@deleteVariationType')->name('variation-types');	
		Route::patch('/variation-types/{warehouse}/{perPage}', 'AssetController@restoreVariationType')->name('variation-types');
		Route::get('/api/search-variation-types/{search}/{perPage}', 'AssetController@searchVariationTypes')->name('search-variation-types');

		Route::get('/api/variations/{perPage}', 'AssetController@showAllVariations')->name('variations');
		Route::post('/variations/{perPage}', 'AssetController@storeNewVariation')->name('variations');	
		Route::put('/variations/{asset}/{perPage}', 'AssetController@updateVariation')->name('variations');	
		Route::delete('/variations/{asset}/{perPage}', 'AssetController@deleteVariation')->name('variations');	
		Route::patch('/variations/{asset}/{perPage}', 'AssetController@restoreVariation')->name('variations');
		Route::get('/api/search-variations/{search}/{perPage}', 'AssetController@searchAllVariations')->name('search-variations');

		Route::get('/api/product-categories/{perPage?}', 'ProductController@showProductAllCategories')->name('product-categories');
		Route::post('/product-categories/{perPage}', 'ProductController@storeProductNewCategory')->name('product-categories');	
		Route::put('/product-categories/{asset}/{perPage}', 'ProductController@updateProductCategory')->name('product-categories');	
		Route::delete('/product-categories/{asset}/{perPage}', 'ProductController@deleteProductCategory')->name('product-categories');	
		Route::patch('/product-categories/{asset}/{perPage}', 'ProductController@restoreProductCategory')->name('product-categories');
		Route::get('/api/search-product-categories/{search}/{perPage}', 'ProductController@searchProductAllCategories')->name('search-product-categories');

		Route::get('/api/products/{perPage}', 'ProductController@showAllProducts')->name('products');
		Route::post('/products/{perPage}', 'ProductController@storeNewProduct')->name('products');	
		Route::put('/products/{product}/{perPage}', 'ProductController@updateProduct')->name('products');
		Route::get('/api/search-products/{search}/{perPage}', 'ProductController@searchAllProducts')->name('search-products');

		Route::get('/api/warehouse-containers/{perPage?}', 'WarehouseController@showAllWarehouseContainers')->name('warehouse-containers');

		Route::get('/api/requisitions/{perPage?}', 'RequisitionController@showAllRequisitions')->name('requisitions');
		Route::get('/api/search-requisitions/{search}/{perPage?}', 'RequisitionController@searchAllRequisitions')->name('search-requisitions');

		Route::get('/api/dispatches/{perPage?}', 'DispatchController@showAllDispatches')->name('dispatches');
		Route::post('/dispatches/{perPage}', 'DispatchController@makeNewDispatch')->name('dispatches');
		Route::get('/api/search-dispatches/{search}/{perPage?}', 'DispatchController@searchAllDispatches')->name('search-dispatches');

		Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

	});

});
