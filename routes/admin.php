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

		Route::get('/api/managers/{perPage}', 'ManagerController@showAllManagers')->name('warhouse-managers');
		Route::post('/managers/{perPage}', 'ManagerController@storeNewManager')->name('warhouse-managers');	
		Route::put('/managers/{manager}/{perPage}', 'ManagerController@updateManager')->name('warhouse-managers');	
		Route::delete('/managers/{manager}/{perPage}', 'ManagerController@deleteManager')->name('warhouse-managers');	
		Route::patch('/managers/{manager}/{perPage}', 'ManagerController@restoreManager')->name('warhouse-managers');
		Route::get('/api/search-managers/{search}/{perPage}', 'ManagerController@searchAllManagers')->name('search-warhouse-managers');


	});

});
