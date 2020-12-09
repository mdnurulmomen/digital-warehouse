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

	Route::get('/{any}', 'HomeController@adminHome')->name('home');

	Route::middleware(['auth:admin'])->group(function () {
		
		Route::get('/api/application-settings', 'SettingController@showApplicationSetting')->name('application-settings');
		Route::put('/payment-settings', 'SettingController@updatePaymentSetting')->name('payment-settings');
		Route::put('/contact-settings', 'SettingController@updateContactSetting')->name('contact-settings');	
		Route::put('/warhouse-settings', 'SettingController@updateWarhouseSetting')->name('warhouse-settings');	
		Route::put('/system-settings', 'SettingController@updateSystemSetting')->name('system-settings');

		Route::get('/api/profile', 'ProfileController@showAdminProfile')->name('profile');	
		Route::put('/profile', 'ProfileController@updateAdminProfile')->name('profile');	
		Route::post('/password', 'ProfileController@updateAdminPassword')->name('password');

		Route::get('/api/owners/{perPage?}', 'WarhouseController@showAllOwners')->name('warhouse-owners');
		Route::post('/owners/{perPage}', 'WarhouseController@storeNewOwner')->name('warhouse-owners');	
		Route::put('/owners/{owner}/{perPage}', 'WarhouseController@updateOwner')->name('warhouse-owners');	
		Route::delete('/owners/{owner}/{perPage}', 'WarhouseController@deleteOwner')->name('warhouse-owners');	
		Route::patch('/owners/{owner}/{perPage}', 'WarhouseController@restoreOwner')->name('warhouse-owners');
		Route::get('/api/search-owners/{search}/{perPage}', 'WarhouseController@searchAllOwners')->name('search-warhouse-owners');

		Route::get('/api/managers/{perPage}', 'ManagerController@showAllManagers')->name('managers');
		Route::post('/managers/{perPage}', 'ManagerController@storeNewManager')->name('managers');	
		Route::put('/managers/{manager}/{perPage}', 'ManagerController@updateManager')->name('managers');	
		Route::delete('/managers/{manager}/{perPage}', 'ManagerController@deleteManager')->name('managers');	
		Route::patch('/managers/{manager}/{perPage}', 'ManagerController@restoreManager')->name('managers');
		Route::get('/api/search-managers/{search}/{perPage}', 'ManagerController@searchAllManagers')->name('search-managers');

		Route::get('/api/merchants/{perPage}', 'MerchantController@showAllMerchants')->name('merchants');
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

		Route::get('/api/warhouses/{perPage}', 'WarhouseController@showAllWarhouses')->name('warhouses');
		Route::post('/warhouses/{perPage}', 'WarhouseController@storeNewWarhouse')->name('warhouses');	
		Route::put('/warhouses/{warhouse}/{perPage}', 'WarhouseController@updateWarhouse')->name('warhouses');	
		Route::delete('/warhouses/{warhouse}/{perPage}', 'WarhouseController@deleteWarhouse')->name('warhouses');	
		Route::patch('/warhouses/{warhouse}/{perPage}', 'WarhouseController@restoreWarhouse')->name('warhouses');
		Route::get('/api/search-warhouses/{search}/{perPage}', 'WarhouseController@searchAllWarhouses')->name('search-warhouses');

		Route::get('/api/rent-periods/{perPage?}', 'AssetController@showAllRentPeriods')->name('rent-periods');
		Route::post('/rent-periods/{perPage}', 'AssetController@storeNewRentPeriod')->name('rent-periods');	
		Route::put('/rent-periods/{period}/{perPage}', 'AssetController@updateRentPeriod')->name('rent-periods');	
		Route::delete('/rent-periods/{period}/{perPage}', 'AssetController@deleteRentPeriod')->name('rent-periods');	
		Route::patch('/rent-periods/{period}/{perPage}', 'AssetController@restoreRentPeriod')->name('rent-periods');
		Route::get('/api/search-rent-periods/{search}/{perPage}', 'AssetController@searchAllRentPeriods')->name('search-rent-periods');

		Route::get('/api/variation-types/{perPage}', 'AssetController@showAllVariationTypes')->name('variationTypes');
		Route::post('/variation-types/{perPage}', 'AssetController@storeVariationType')->name('variationTypes');	
		Route::put('/variation-types/{warhouse}/{perPage}', 'AssetController@updateVariationType')->name('variationTypes');	
		Route::delete('/variation-types/{warhouse}/{perPage}', 'AssetController@deleteVariationType')->name('variationTypes');	
		Route::patch('/variation-types/{warhouse}/{perPage}', 'AssetController@restoreVariationType')->name('variationTypes');
		Route::get('/api/search-variation-types/{search}/{perPage}', 'AssetController@searchVariationTypes')->name('search-variationTypes');

	});

});
