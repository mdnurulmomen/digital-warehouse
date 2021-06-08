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

		// profile
		Route::get('/api/profile', 'ProfileController@showOwnerProfile')->name('profile');	
		Route::put('/profile', 'ProfileController@updateOwnerProfile')->name('profile');	
		Route::post('/password', 'ProfileController@updateOwnerPassword')->name('password');

		// Route::get('/api/current-owner', 'OwnerController@currentOwner')->name('current-user');

		// my-warehouses
		Route::get('/my-warehouses/{perPage}', 'OwnerController@showOwnerAllWarehouses')->name('my-warehouses');
		Route::get('/search-my-warehouses/{search}/{perPage}', 'OwnerController@searchOwnerAllWarehouses')->name('search-warehouses');

		/* Special Routes */

		// application setting
		Route::get('/api/application-settings', 'SettingController@showApplicationSetting')->name('application-settings');
		Route::put('/payment-settings', 'SettingController@updatePaymentSetting')->name('payment-settings');
		Route::put('/contact-settings', 'SettingController@updateContactSetting')->name('contact-settings');	
		Route::put('/warehouse-settings', 'SettingController@updateWarehouseSetting')->name('warehouse-settings');	
		Route::put('/system-settings', 'SettingController@updateSystemSetting')->name('system-settings');

		// warehouse owner
		Route::get('/api/owners/{perPage?}', 'WarehouseController@showAllOwners')->name('warehouse-owners');
		Route::post('/owners/{perPage}', 'WarehouseController@storeNewOwner')->name('warehouse-owners');	
		Route::put('/owners/{owner}/{perPage}', 'WarehouseController@updateOwner')->name('warehouse-owners');	
		Route::delete('/owners/{owner}/{perPage}', 'WarehouseController@deleteOwner')->name('warehouse-owners');	
		Route::patch('/owners/{owner}/{perPage}', 'WarehouseController@restoreOwner')->name('warehouse-owners');
		Route::get('/api/search-owners/{search}/{perPage}', 'WarehouseController@searchAllOwners')->name('search-warehouse-owners');

		// warehouse
		Route::get('/api/warehouses/{perPage?}', 'WarehouseController@showAllWarehouses')->name('warehouses');
		Route::post('/warehouses/{perPage}', 'WarehouseController@storeNewWarehouse')->name('warehouses');	
		Route::put('/warehouses/{warehouse}/{perPage}', 'WarehouseController@updateWarehouse')->name('warehouses');	
		Route::delete('/warehouses/{warehouse}/{perPage}', 'WarehouseController@deleteWarehouse')->name('warehouses');	
		Route::patch('/warehouses/{warehouse}/{perPage}', 'WarehouseController@restoreWarehouse')->name('warehouses');
		Route::get('/api/search-warehouses/{search}/{perPage}', 'WarehouseController@searchAllWarehouses')->name('search-warehouses');

		// warehouse container
		Route::get('/api/warehouse-containers/{warehouse}/{perPage?}', 'WarehouseController@showAllWarehouseContainers')->name('warehouse-containers');

		// manager
		Route::get('/api/managers/{perPage?}', 'ManagerController@showAllManagers')->name('managers');
		Route::post('/managers/{perPage}', 'ManagerController@storeNewManager')->name('managers');	
		Route::put('/managers/{manager}/{perPage}', 'ManagerController@updateManager')->name('managers');	
		Route::delete('/managers/{manager}/{perPage}', 'ManagerController@deleteManager')->name('managers');	
		Route::patch('/managers/{manager}/{perPage}', 'ManagerController@restoreManager')->name('managers');
		Route::get('/api/search-managers/{search}/{perPage}', 'ManagerController@searchAllManagers')->name('search-managers');

		// merchant
		Route::get('/api/merchants/{perPage?}', 'MerchantController@showAllMerchants')->name('merchants');
		Route::post('/merchants/{perPage}', 'MerchantController@storeNewMerchant')->name('merchants');	
		Route::put('/merchants/{merchant}/{perPage}', 'MerchantController@updateMerchant')->name('merchants');	
		Route::delete('/merchants/{merchant}/{perPage}', 'MerchantController@deleteMerchant')->name('merchants');
		Route::patch('/merchants/{merchant}/{perPage}', 'MerchantController@restoreMerchant')->name('merchants');
		Route::get('/api/search-merchants/{search}/{perPage}', 'MerchantController@searchAllMerchants')->name('search-merchants');

		// storage type
		Route::get('/api/storage-types/{perPage?}','AssetController@showAllStorageTypes')->name('storage-types');
		Route::post('/storage-types/{perPage}','AssetController@storeNewStorageType')->name('storage-types');	
		Route::put('/storage-types/{type}/{perPage}','AssetController@updateStorageType')->name('storage-types');	
		Route::delete('/storage-types/{type}/{perPage}','AssetController@deleteStorageType')->name('storage-types');	
		Route::patch('/storage-types/{type}/{perPage}','AssetController@restoreStorageType')->name('storage-types');
		Route::get('/api/search-storage-types/{search}/{perPage}','AssetController@searchAllStorageTypes')->name('search-storage-types');

		// container
		Route::get('/api/containers/{perPage?}', 'AssetController@showAllContainers')->name('containers');
		Route::post('/containers/{perPage}', 'AssetController@storeNewContainer')->name('containers');	
		Route::put('/containers/{container}/{perPage}', 'AssetController@updateContainer')->name('containers');	
		Route::delete('/containers/{container}/{perPage}', 'AssetController@deleteContainer')->name('containers');	
		Route::patch('/containers/{container}/{perPage}', 'AssetController@restoreContainer')->name('containers');
		Route::get('/api/search-containers/{search}/{perPage}', 'AssetController@searchAllContainers')->name('search-containers');

		// rent period
		Route::get('/api/rent-periods/{perPage?}', 'AssetController@showAllRentPeriods')->name('rent-periods');
		Route::post('/rent-periods/{perPage}', 'AssetController@storeNewRentPeriod')->name('rent-periods');	
		Route::put('/rent-periods/{period}/{perPage}', 'AssetController@updateRentPeriod')->name('rent-periods');	
		Route::delete('/rent-periods/{period}/{perPage}', 'AssetController@deleteRentPeriod')->name('rent-periods');	
		Route::patch('/rent-periods/{period}/{perPage}', 'AssetController@restoreRentPeriod')->name('rent-periods');
		Route::get('/api/search-rent-periods/{search}/{perPage}', 'AssetController@searchAllRentPeriods')->name('search-rent-periods');

		// variation type
		Route::get('/api/variation-types/{perPage?}', 'AssetController@showAllVariationTypes')->name('variation-types');
		Route::post('/variation-types/{perPage}', 'AssetController@storeVariationType')->name('variation-types');	
		Route::put('/variation-types/{warehouse}/{perPage}', 'AssetController@updateVariationType')->name('variation-types');	
		Route::delete('/variation-types/{warehouse}/{perPage}', 'AssetController@deleteVariationType')->name('variation-types');	
		Route::patch('/variation-types/{warehouse}/{perPage}', 'AssetController@restoreVariationType')->name('variation-types');
		Route::get('/api/search-variation-types/{search}/{perPage}', 'AssetController@searchVariationTypes')->name('search-variation-types');

		// variation
		Route::get('/api/variations/{perPage}', 'AssetController@showAllVariations')->name('variations');
		Route::post('/variations/{perPage}', 'AssetController@storeNewVariation')->name('variations');	
		Route::put('/variations/{asset}/{perPage}', 'AssetController@updateVariation')->name('variations');	
		Route::delete('/variations/{asset}/{perPage}', 'AssetController@deleteVariation')->name('variations');	
		Route::patch('/variations/{asset}/{perPage}', 'AssetController@restoreVariation')->name('variations');
		Route::get('/api/search-variations/{search}/{perPage}', 'AssetController@searchAllVariations')->name('search-variations');

		// product-category
		Route::get('/api/product-categories/{perPage?}', 'ProductController@showProductAllCategories')->name('product-categories');
		Route::post('/product-categories/{perPage}', 'ProductController@storeProductNewCategory')->name('product-categories');	
		Route::put('/product-categories/{asset}/{perPage}', 'ProductController@updateProductCategory')->name('product-categories');	
		Route::delete('/product-categories/{asset}/{perPage}', 'ProductController@deleteProductCategory')->name('product-categories');	
		Route::patch('/product-categories/{asset}/{perPage}', 'ProductController@restoreProductCategory')->name('product-categories');
		Route::get('/api/search-product-categories/{search}/{perPage}', 'ProductController@searchProductAllCategories')->name('search-product-categories');

		// product
		Route::get('/api/products/{perPage}', 'ProductController@showAllProducts')->name('products');
		Route::post('/products/{perPage}', 'ProductController@storeNewProduct')->name('products');	
		Route::put('/products/{product}/{perPage}', 'ProductController@updateProduct')->name('products');
		Route::get('/api/search-products/{search}/{perPage}', 'ProductController@searchAllProducts')->name('search-products');

		// product-stock
		Route::get('/api/product-stocks/{product}/{perPage?}', 'ProductController@showProductAllStocks')->name('product-stocks');
		Route::post('/product-stocks/{perPage?}', 'ProductController@storeProductStock')->name('product-stocks');
		Route::put('/product-stocks/{stock}/{perPage?}', 'ProductController@updateProductStock')->name('product-stocks');
		Route::delete('/product-stocks/{stock}/{perPage?}', 'ProductController@deleteProductStock')->name('product-stocks');
		Route::patch('/product-stocks/{stock}/{perPage?}', 'ProductController@restoreProductStock')->name('product-stocks');
		Route::get('/api/search-product-stocks/{product}/{search}/{perPage?}', 'ProductController@searchProductAllStocks')->name('search-product-stocks');

		// requisition
		Route::get('/api/requisitions/{perPage?}', 'RequisitionController@showAllRequisitions')->name('requisitions');
		Route::put('/requisitions/{requisition}/{perPage}', 'RequisitionController@cancelRequisition')->name('requisitions');
		Route::get('/api/search-requisitions/{search}/{perPage?}', 'RequisitionController@searchAllRequisitions')->name('search-requisitions');

		// dispatch
		Route::get('/api/dispatches/{perPage?}', 'DispatchController@showAllDispatches')->name('dispatches');
		Route::post('/dispatches/{perPage}', 'DispatchController@makeDispatch')->name('dispatches');
		Route::get('/api/search-dispatches/{search}/{perPage?}', 'DispatchController@searchAllDispatches')->name('search-dispatches');

		// warehouse-managers
		/*
			Route::get('/api/warehouse-managers/{perPage?}','WarehouseController@showAllWarehouseManagers')->name('warehouse-managers');
			// Route::post('/warehouse-managers/{perPage}','WarehouseController@storeNewWarehouseManager')->name('warehouse-managers');	
			Route::put('/warehouse-managers/{warehouse}/{perPage}','WarehouseController@updateWarehouseManager')->name('warehouse-managers');	
			Route::delete('/warehouse-managers/{warehouse}/{perPage}','WarehouseController@deleteWarehouseManager')->name('warehouse-managers');
			Route::get('/api/search-warehouse-managers/{search}/{perPage}','WarehouseController@searchAllWarehouseManagers')->name('search-warehouse-managers');
		*/

		// roles
		Route::get('/api/roles/{perPage?}','RoleController@showAllRoles')->name('roles');
		Route::post('/roles/{perPage}','RoleController@storeNewRole')->name('roles');	
		Route::put('/roles/{role}/{perPage}','RoleController@updateRole')->name('roles');	
		Route::delete('/roles/{role}/{perPage}','RoleController@deleteRole')->name('roles');
		Route::get('/api/search-roles/{search}/{perPage}','RoleController@searchAllRoles')->name('search-roles');

		// packaging-packages
		Route::get('/api/packaging-packages/{perPage?}','AssetController@showAllPackagingPackages')->name('packaging-packages');
		Route::post('/packaging-packages/{perPage}','AssetController@storeNewPackagingPackage')->name('packaging-packages');	
		Route::put('/packaging-packages/{package}/{perPage}','AssetController@updatePackagingPackage')->name('packaging-packages');	
		Route::delete('/packaging-packages/{package}/{perPage}','AssetController@deletePackagingPackage')->name('packaging-packages');
		Route::patch('/packaging-packages/{package}/{perPage}','AssetController@restorePackagingPackage')->name('packaging-packages');
		Route::get('/api/search-packaging-packages/{search}/{perPage}','AssetController@searchAllPackagingPackages')->name('search-packaging-packages');

		// permission
		Route::get('/api/permissions/','RoleController@showAllPermissions')->name('permissions');

		// first dashboard
		Route::get('/api/general-dashboard-one','AnalyticsController@getGeneralDashboardOneData')->name('dashboard-one');
		// second dashboard
		Route::get('/api/general-dashboard-two','AnalyticsController@getGeneralDashboardTwoData')->name('dashboard-two');
		
		Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

		Route::fallback(function () {

			$access_token = Illuminate\Support\Str::random(60);
        	$roles = \Auth::guard('owner')->user()->roles;
        	$permissions = Auth::guard('owner')->user()->permissions;
			return view('layouts.owner', ['permissions' => $permissions, 'roles' => $roles, 'access_token' => $access_token]);
		    
		});
	
	});

});
