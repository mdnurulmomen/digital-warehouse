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
		
		Route::get('/api/mails/{perPage?}', 'MailController@showAllMails')->name('mails.index');
		Route::post('/mails/{perPage}', 'MailController@sendDynamicMail')->name('mails.store');
		Route::delete('/mails/{mail}/{perPage}', 'MailController@deleteMail')->name('mails.update');
		Route::get('api/search-mails/{query}/{perPage}', 'MailController@searchAllMails')->name('search-mails');

		// profile
		Route::get('/api/profile', 'ProfileController@showAdminProfile')->name('profile.show');	
		Route::put('/profile', 'ProfileController@updateAdminProfile')->name('profile.update');	
		Route::post('/password', 'ProfileController@updateAdminPassword')->name('password.update');

		// application setting
		Route::get('/api/application-settings', 'SettingController@showApplicationSetting')->name('application-settings.show');
		Route::put('/payment-settings', 'SettingController@updatePaymentSetting')->name('payment-settings.update');
		Route::put('/contact-settings', 'SettingController@updateContactSetting')->name('contact-settings.update');	
		Route::put('/warehouse-settings', 'SettingController@updateWarehouseSetting')->name('warehouse-settings.update');	
		Route::put('/system-settings', 'SettingController@updateSystemSetting')->name('system-settings.update');
		Route::put('/media-settings', 'SettingController@updateMediaSetting')->name('media-settings.update');

		// warehouse owner
		Route::get('/api/owners/{perPage?}', 'WarehouseController@showAllOwners')->name('warehouse-owners.index');
		Route::post('/owners/{perPage}', 'WarehouseController@storeNewOwner')->name('warehouse-owners.store');	
		Route::put('/owners/{owner}/{perPage}', 'WarehouseController@updateOwner')->name('warehouse-owners.update');	
		Route::delete('/owners/{owner}/{perPage}', 'WarehouseController@deleteOwner')->name('warehouse-owners.delete');	
		Route::patch('/owners/{owner}/{perPage}', 'WarehouseController@restoreOwner')->name('warehouse-owners.restore');
		Route::get('/api/search-owners/{search}/{perPage}', 'WarehouseController@searchAllOwners')->name('search-warehouse-owners');

		// warehouse
		Route::get('/api/warehouses/{perPage?}', 'WarehouseController@showAllWarehouses')->name('warehouses.index');
		Route::post('/warehouses/{perPage}', 'WarehouseController@storeNewWarehouse')->name('warehouses.store');	
		Route::put('/warehouses/{warehouse}/{perPage}', 'WarehouseController@updateWarehouse')->name('warehouses.update');	
		Route::delete('/warehouses/{warehouse}/{perPage}', 'WarehouseController@deleteWarehouse')->name('warehouses.delete');	
		Route::patch('/warehouses/{warehouse}/{perPage}', 'WarehouseController@restoreWarehouse')->name('warehouses.restore');
		Route::get('/api/search-warehouses/{search}/{perPage}', 'WarehouseController@searchAllWarehouses')->name('search-warehouses');

		// merchant-warehouses
		Route::get('/api/dealt-warehouses/{merchant}/{warehouse?}/{requiredSpaceType?}/{perPage?}', 'WarehouseController@showMerchantWarehouses')->name('merchant-warehouses.index');

		// warehouse empty containers
		Route::get('/api/warehouse-empty-containers/{warehouse?}', 'WarehouseController@showAllWarehouseEmptySpaces')->name('warehouse-empty-containers.index');

		// warehouse-containers
		Route::get('/api/warehouses/{warehouse}/containers/{perPage?}', 'WarehouseController@showWarehouseAllContainers')->name('warehouse-containers.index');
		Route::get('/api/warehouses/{warehouse}/search-containers/{search}/{perPage?}', 'WarehouseController@searchWarehouseAllContainers')->name('search-warehouse-containers');

		// warehouse-container-shelves
		Route::get('/api/warehouse-containers/{container}/shelves/{perPage?}', 'WarehouseController@showContainerAllShelves')->name('warehouse-container-shelves.index');
		Route::get('/api/warehouse-containers/{container}/search-shelves/{search}/{perPage?}', 'WarehouseController@searchContainerAllShelves')->name('search-warehouse-container-shelves.index');

		// warehouse-container-shelf-units
		Route::get('/api/warehouse-container-shelves/{shelf}/units/{perPage?}', 'WarehouseController@showShelfAllUnits')->name('warehouse-container-shelf-units.index');
		Route::get('/api/warehouse-container-shelves/{shelf}/search-units/{search}/{perPage?}', 'WarehouseController@searchShelfAllUnits')->name('search-warehouse-container-shelf-units');

		// manager
		Route::get('/api/managers/{perPage?}', 'ManagerController@showAllManagers')->name('managers.index');
		Route::post('/managers/{perPage}', 'ManagerController@storeNewManager')->name('managers.store');	
		Route::put('/managers/{manager}/{perPage}', 'ManagerController@updateManager')->name('managers.update');	
		Route::delete('/managers/{manager}/{perPage}', 'ManagerController@deleteManager')->name('managers.delete');	
		Route::patch('/managers/{manager}/{perPage}', 'ManagerController@restoreManager')->name('managers.restore');
		Route::get('/api/search-managers/{search}/{perPage}', 'ManagerController@searchAllManagers')->name('search-managers');

		// merchant
		Route::get('/api/merchants/{perPage?}', 'MerchantController@showAllMerchants')->name('merchants.index');
		Route::post('/merchants/{perPage}', 'MerchantController@storeNewMerchant')->name('merchants.store');	
		Route::put('/merchants/{merchant}/{perPage}', 'MerchantController@updateMerchant')->name('merchants.update');	
		Route::delete('/merchants/{merchant}/{perPage}', 'MerchantController@deleteMerchant')->name('merchants.delete');
		Route::patch('/merchants/{merchant}/{perPage}', 'MerchantController@restoreMerchant')->name('merchants.restore');
		Route::get('/api/search-merchants/{search}/{perPage}', 'MerchantController@searchAllMerchants')->name('search-merchants');

		// storage type
		Route::get('/api/storage-types/{perPage?}','AssetController@showAllStorageTypes')->name('storage-types.index');
		Route::post('/storage-types/{perPage}','AssetController@storeNewStorageType')->name('storage-types.store');	
		Route::put('/storage-types/{type}/{perPage}','AssetController@updateStorageType')->name('storage-types.update');	
		Route::delete('/storage-types/{type}/{perPage}','AssetController@deleteStorageType')->name('storage-types.delete');	
		Route::patch('/storage-types/{type}/{perPage}','AssetController@restoreStorageType')->name('storage-types.restore');
		Route::get('/api/search-storage-types/{search}/{perPage}','AssetController@searchAllStorageTypes')->name('search-storage-types');

		// container type
		Route::get('/api/container-types/{perPage?}','AssetController@showAllContainerTypes')->name('container-types.index');
		Route::post('/container-types/{perPage}','AssetController@storeNewContainerType')->name('container-types.store');	
		Route::put('/container-types/{type}/{perPage}','AssetController@updateContainerType')->name('container-types.update');	
		Route::delete('/container-types/{type}/{perPage}','AssetController@deleteContainerType')->name('container-types.delete');	
		Route::patch('/container-types/{type}/{perPage}','AssetController@restoreContainerType')->name('container-types.restore');
		Route::get('/api/search-container-types/{search}/{perPage}','AssetController@searchAllContainerTypes')->name('search-container-types');

		// container
		Route::get('/api/containers/{perPage?}', 'AssetController@showAllContainers')->name('containers.index');
		Route::post('/containers/{perPage}', 'AssetController@storeNewContainer')->name('containers.store');	
		Route::put('/containers/{container}/{perPage}', 'AssetController@updateContainer')->name('containers.update');	
		Route::delete('/containers/{container}/{perPage}', 'AssetController@deleteContainer')->name('containers.delete');	
		Route::patch('/containers/{container}/{perPage}', 'AssetController@restoreContainer')->name('containers.restore');
		Route::get('/api/search-containers/{search}/{perPage}', 'AssetController@searchAllContainers')->name('search-containers');

		// rent period
		Route::get('/api/rent-periods/{perPage?}', 'AssetController@showAllRentPeriods')->name('rent-periods.index');
		Route::post('/rent-periods/{perPage}', 'AssetController@storeNewRentPeriod')->name('rent-periods.store');	
		Route::put('/rent-periods/{period}/{perPage}', 'AssetController@updateRentPeriod')->name('rent-periods.update');	
		Route::delete('/rent-periods/{period}/{perPage}', 'AssetController@deleteRentPeriod')->name('rent-periods.delete');	
		Route::patch('/rent-periods/{period}/{perPage}', 'AssetController@restoreRentPeriod')->name('rent-periods.restore');
		Route::get('/api/search-rent-periods/{search}/{perPage}', 'AssetController@searchAllRentPeriods')->name('search-rent-periods');

		// variation type
		Route::get('/api/variation-types/{perPage?}', 'AssetController@showAllVariationTypes')->name('variation-types.index');
		Route::post('/variation-types/{perPage}', 'AssetController@storeVariationType')->name('variation-types.store');	
		Route::put('/variation-types/{warehouse}/{perPage}', 'AssetController@updateVariationType')->name('variation-types.update');	
		Route::delete('/variation-types/{warehouse}/{perPage}', 'AssetController@deleteVariationType')->name('variation-types.delete');	
		Route::patch('/variation-types/{warehouse}/{perPage}', 'AssetController@restoreVariationType')->name('variation-types.restore');
		Route::get('/api/search-variation-types/{search}/{perPage}', 'AssetController@searchVariationTypes')->name('search-variation-types');

		// variation
		Route::get('/api/variations/{perPage}', 'AssetController@showAllVariations')->name('variations.index');
		Route::post('/variations/{perPage}', 'AssetController@storeNewVariation')->name('variations.store');	
		Route::put('/variations/{asset}/{perPage}', 'AssetController@updateVariation')->name('variations.update');	
		Route::delete('/variations/{asset}/{perPage}', 'AssetController@deleteVariation')->name('variations.delete');	
		Route::patch('/variations/{asset}/{perPage}', 'AssetController@restoreVariation')->name('variations.restore');
		Route::get('/api/search-variations/{search}/{perPage}', 'AssetController@searchAllVariations')->name('search-variations');

		// product-category
		Route::get('/api/manufacturers/{perPage?}', 'ProductController@showAllManufacturers')->name('manufacturers.index');
		Route::post('/manufacturers/{perPage}', 'ProductController@storeNewManufacturer')->name('manufacturers.store');	
		Route::put('/manufacturers/{asset}/{perPage}', 'ProductController@updateManufacturer')->name('manufacturers.update');	
		Route::delete('/manufacturers/{asset}/{perPage}', 'ProductController@deleteManufacturer')->name('manufacturers.delete');	
		Route::patch('/manufacturers/{asset}/{perPage}', 'ProductController@restoreManufacturer')->name('manufacturers.restore');
		Route::get('/api/search-manufacturers/{search}/{perPage}', 'ProductController@searchAllManufacturers')->name('search-manufacturers');

		// product-category
		Route::get('/api/product-categories/{perPage?}', 'ProductController@showProductAllCategories')->name('product-categories.index');
		Route::post('/product-categories/{perPage}', 'ProductController@storeProductNewCategory')->name('product-categories.store');	
		Route::put('/product-categories/{asset}/{perPage}', 'ProductController@updateProductCategory')->name('product-categories.update');	
		Route::delete('/product-categories/{asset}/{perPage}', 'ProductController@deleteProductCategory')->name('product-categories.delete');	
		Route::patch('/product-categories/{asset}/{perPage}', 'ProductController@restoreProductCategory')->name('product-categories.restore');
		Route::get('/api/search-product-categories/{search}/{perPage}', 'ProductController@searchProductAllCategories')->name('search-product-categories');

		// category-products
		Route::get('/api/category-products/{category}/{perPage?}', 'ProductController@showCategoryAllProducts')->name('category-products.index');
		Route::post('/category-products/{perPage}', 'ProductController@storeCategoryNewProduct')->name('category-products.store');	
		Route::put('/category-products/{product}/{perPage}', 'ProductController@updateCategoryProduct')->name('category-products.update');
		Route::get('/api/search-category-products/{category}/{search}/{perPage?}', 'ProductController@searchCategoryAllProducts')->name('search-category-products');

		// product
		Route::get('/api/products/{perPage?}', 'ProductController@showAllProducts')->name('products.show');
		Route::post('/products/{perPage}', 'ProductController@storeNewProduct')->name('products.store');	
		Route::put('/products/{product}/{perPage}', 'ProductController@updateProduct')->name('products.update');
		Route::delete('/products/{product}/{perPage}', 'ProductController@deleteProduct')->name('products.delete');
		Route::patch('/products/{product}/{perPage}', 'ProductController@restoreProduct')->name('products.restore');
		Route::get('/api/search-products/{search}/{perPage}', 'ProductController@searchAllProducts')->name('search-products');

		// product-stock
		Route::get('/api/product-stocks/{productMerchant}/{perPage?}', 'ProductController@showProductAllStocks')->name('product-stocks.index');
		Route::post('/product-stocks/{perPage}', 'ProductController@storeProductStock')->name('product-stocks.store');
		Route::put('/product-stocks/{stock}/{perPage}', 'ProductController@updateProductStock')->name('product-stocks.update');
		Route::delete('/product-stocks/{stock}/{perPage}', 'ProductController@deleteProductStock')->name('product-stocks.delete');
		// Route::patch('/product-stocks/{stock}/{perPage}', 'ProductController@restoreProductStock')->name('product-stocks');
		Route::post('/api/search-product-stocks/{productMerchant}/{perPage}', 'ProductController@searchProductAllStocks')->name('search-product-stocks');

		// stock
		Route::get('/api/stocks/{perPage?}', 'ProductController@showAllStocks')->name('stocks.index');
		Route::post('/stocks/{perPage}', 'ProductController@storeStock')->name('stocks.store');
		Route::put('/stocks/{stock}/{perPage}', 'ProductController@updateStock')->name('stocks.update');
		Route::delete('/stocks/{stock}/{perPage}', 'ProductController@deleteStock')->name('stocks.delete');
		Route::post('/api/search-stocks/{perPage}', 'ProductController@searchAllStocks')->name('search-stocks');

		// requisition
		Route::get('/api/requisitions/{perPage?}', 'RequisitionController@showAllRequisitions')->name('requisitions.index');
		Route::post('/requisitions/{perPage}', 'RequisitionController@makeNewRequisition')->name('requisitions.store');
		Route::put('/requisitions/{requisition}/{perPage}', 'RequisitionController@cancelRequisition')->name('requisitions.update');
		Route::post('/search-requisitions/{perPage?}', 'RequisitionController@searchAllRequisitions')->name('search-requisitions');

		// Merchant-Requisitions
		Route::get('/api/merchant-product-requisitions/{merchantProduct}/{perPage?}', 'RequisitionController@showMerchantProductAllRequisitions')->name('merchant-product-requisitions.index');
		Route::post('/search-merchant-product-requisitions/{perPage?}', 'RequisitionController@searchMerchantProductAllRequisitions')->name('search-merchant-product-requisitions');

		// dispatch
		// Route::get('/api/dispatches/{perPage?}', 'DispatchController@showAllDispatches')->name('dispatches');
		Route::post('/dispatches/{perPage}', 'DispatchController@makeDispatch')->name('dispatches.store');
		// Route::get('/api/search-dispatches/{search}/{perPage?}', 'DispatchController@searchAllDispatches')->name('search-dispatches');

		// warehouse-managers
		/*
			Route::get('/api/warehouse-managers/{perPage?}','WarehouseController@showAllWarehouseManagers')->name('warehouse-managers');
			// Route::post('/warehouse-managers/{perPage}','WarehouseController@storeNewWarehouseManager')->name('warehouse-managers');	
			Route::put('/warehouse-managers/{warehouse}/{perPage}','WarehouseController@updateWarehouseManager')->name('warehouse-managers');	
			Route::delete('/warehouse-managers/{warehouse}/{perPage}','WarehouseController@deleteWarehouseManager')->name('warehouse-managers');
			Route::get('/api/search-warehouse-managers/{search}/{perPage}','WarehouseController@searchAllWarehouseManagers')->name('search-warehouse-managers');
		*/

		// roles
		Route::get('/api/roles/{perPage?}','RoleController@showAllRoles')->name('roles.index');
		Route::post('/roles/{perPage}','RoleController@storeNewRole')->name('roles.store');	
		Route::put('/roles/{role}/{perPage}','RoleController@updateRole')->name('roles.update');	
		Route::delete('/roles/{role}/{perPage}','RoleController@deleteRole')->name('roles.delete');
		Route::get('/api/search-roles/{search}/{perPage}','RoleController@searchAllRoles')->name('search-roles');
		
		// packaging-packages
		Route::get('/api/packaging-packages/{perPage?}','AssetController@showAllPackagingPackages')->name('packaging-packages.index');
		Route::post('/packaging-packages/{perPage}','AssetController@storeNewPackagingPackage')->name('packaging-packages.store');	
		Route::put('/packaging-packages/{package}/{perPage}','AssetController@updatePackagingPackage')->name('packaging-packages.update');	
		Route::delete('/packaging-packages/{package}/{perPage}','AssetController@deletePackagingPackage')->name('packaging-packages.delete');
		Route::patch('/packaging-packages/{package}/{perPage}','AssetController@restorePackagingPackage')->name('packaging-packages.restore');
		Route::get('/api/search-packaging-packages/{search}/{perPage}','AssetController@searchAllPackagingPackages')->name('search-packaging-packages');

		// delivery-companies
		Route::get('/api/delivery-companies/{perPage?}','AssetController@showDeliveryAllCompanies')->name('delivery-companies.index');
		Route::post('/delivery-companies/{perPage}','AssetController@storeDeliveryNewCompany')->name('delivery-companies.store');	
		Route::put('/delivery-companies/{company}/{perPage}','AssetController@updateDeliveryCompany')->name('delivery-companies.update');	
		Route::delete('/delivery-companies/{company}/{perPage}','AssetController@deleteDeliveryCompany')->name('delivery-companies.delete');
		Route::patch('/delivery-companies/{company}/{perPage}','AssetController@restoreDeliveryCompany')->name('delivery-companies.restore');
		Route::get('/api/search-delivery-companies/{search}/{perPage}','AssetController@searchDeliveryAllCompanies')->name('search-delivery-companies');

		// product-merchants
		Route::get('/api/product-merchants/{product}/{perPage}', 'ProductController@showProductAllMerchants')->name('product-merchants.index');
		Route::post('/product-merchants/{perPage}', 'ProductController@storeProductNewMerchant')->name('product-merchants.store');	
		Route::put('/product-merchants/{productMerchant}/{perPage}', 'ProductController@updateProductMerchant')->name('product-merchants.update');
		Route::delete('/product-merchants/{productMerchant}/{perPage}', 'ProductController@deleteProductMerchant')->name('product-merchants.delete');
		Route::get('/api/search-product-merchants/{product}/{search}/{perPage}', 'ProductController@searchProductAllMerchants')->name('search-product-merchants');

		// merchant-products
		Route::get('/api/merchant-all-products/{merchant}', 'MerchantController@showMerchantAllProducts')->name('merchant-all-products.index');
		Route::get('/api/merchant-products/{merchant}/{perPage?}', 'MerchantController@showMerchantAvailableProducts')->name('merchant-products.show');
		Route::post('/merchant-products/{perPage}', 'MerchantController@storeMerchantNewProduct')->name('merchant-products.store');	
		Route::put('/merchant-products/{productMerchant}/{perPage}', 'MerchantController@updateMerchantProduct')->name('merchant-products.update');
		Route::delete('/merchant-products/{productMerchant}/{perPage}', 'MerchantController@deleteMerchantProduct')->name('merchant-products.delete');
		Route::post('/search-merchant-products/{perPage}', 'MerchantController@searchMerchantAllProducts')->name('search-merchant-products');

		// merchant-deals
		Route::get('/api/merchant-deals/{merchant}/{perPage?}','DealController@showMerchantAllDeals')->name('merchant-deals.index');
		Route::post('/merchant-deals/{perPage}','DealController@storeMerchantDeal')->name('merchant-deals.store');	
		Route::put('/merchant-deals/{deal}/{perPage}','DealController@updateMerchantDeal')->name('merchant-deals.update');	
		Route::delete('/merchant-deals/{deal}/{perPage}','DealController@deleteMerchantDeal')->name('merchant-deals.delete');
		Route::post('/search-merchant-deals/{perPage}','DealController@searchMerchantAllDeals')->name('search-merchant-deals');

		// deal-payments
		Route::get('/api/deal-payments/{deal}/{perPage?}', 'DealController@showDealAllPayments')->name('deal-payments.index');
		Route::post('/deal-payments/{perPage}', 'DealController@storeDealNewPayment')->name('deal-payments.store');	
		Route::put('/deal-payments/{payment}/{perPage}', 'DealController@updateDealPayment')->name('deal-payments.update');	
		Route::delete('/deal-payments/{payment}/{perPage}', 'DealController@deleteDealPayment')->name('deal-payments.delete');
		Route::post('/api/search-deal-payments/{perPage}', 'DealController@searchDealAllPayments')->name('search-deal-payments');

		// merchant-agents
		Route::get('/api/merchant-agents/{merchant}/{perPage?}', 'MerchantController@showMerchantAllAgents')->name('merchant-agents.index');

		// permission
		Route::get('/api/permissions/','RoleController@showAllPermissions')->name('permissions.index');

		// first dashboard
		Route::get('/api/general-dashboard-one','AnalyticsController@getGeneralDashboardOneData')->name('dashboard-one.show');
		// second dashboard
		Route::get('/api/general-dashboard-two','AnalyticsController@getGeneralDashboardTwoData')->name('dashboard-two.show');

		// imports
		Route::post('import-products', 'ImportController@importProducts')->name('import-products');
		Route::post('import-merchant-products', 'ImportController@importMerchantProducts')->name('import-merchant-products');

		// admin logout
		Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

		Route::fallback(function () {
			$access_token = Illuminate\Support\Str::random(60);
        	$roles = \Auth::guard('admin')->user()->roles;
        	$permissions = Auth::guard('admin')->user()->permissions;
        	$generalSettings = \App\Models\ApplicationSetting::firstOrCreate([]);
			return view('layouts.admin', ['permissions' => $permissions, 'roles' => $roles, 'access_token' => $access_token, 'general_settings' => $generalSettings]);
		});

	});

});
