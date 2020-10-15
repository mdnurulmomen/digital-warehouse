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

		Route::get('/api/owners/{perPage}', 'WarhouseController@showAllOwners')->name('warhouse-owners');
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

		Route::get('/api/storage-types/{perPage}','AssetController@showAllStorageTypes')->name('storage-types');
		Route::post('/storage-types/{perPage}','AssetController@storeNewStorageType')->name('storage-types');	
		Route::put('/storage-types/{type}/{perPage}','AssetController@updateStorageType')->name('storage-types');	
		Route::delete('/storage-types/{type}/{perPage}','AssetController@deleteStorageType')->name('storage-types');	
		Route::patch('/storage-types/{type}/{perPage}','AssetController@restoreStorageType')->name('storage-types');
		Route::get('/api/search-storage-types/{search}/{perPage}','AssetController@searchAllStorageTypes')->name('search-storage-types');


	});

});
