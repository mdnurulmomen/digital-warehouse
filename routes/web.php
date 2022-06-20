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

Auth::routes();

Route::get('/clear-cache', function() {
    
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    // Artisan::call('event:clear');
    // Artisan::call('optimize:clear');
    return 'All cache has been flushed';

});

Route::name('merchant.')->group(function () {

	Route::middleware(['auth'])->group(function () {

		Route::get('/{any}', 'HomeController@index')->name('home');

		// API's
		// profile
		Route::get('/api/profile', 'ProfileController@showMerchantProfile')->name('profile');	
		Route::put('/profile', 'ProfileController@updateMerchantProfile')->name('profile');	
		Route::post('/password', 'ProfileController@updateMerchantPassword')->name('password');
		
		/* complementary routes for merchants (my-properties) */
		Route::get('/api/my-agents/{perPage?}', 'MerchantController@showMyAllAgents')->name('my-agents');

		// my-properties
		Route::get('/api/my-products/{perPage?}', 'MerchantController@showMyAllProducts')->name('my-products');
		Route::post('/search-my-products/{perPage}', 'MerchantController@searchMyAllProducts')->name('my-products');

		Route::get('/api/my-requisitions/{perPage?}', 'MerchantController@showMyAllRequisitions')->name('my-requisitions');
		Route::post('/requisitions/{perPage}', 'MerchantController@makeNewRequisition')->name('my-requisitions');
		Route::post('/api/search-my-requisitions/{perPage}', 'MerchantController@searchMyAllRequisitions')->name('search-my-requisitions');

		// Receive products
		Route::post('/receive-dispatched-products/{perPage}', 'MerchantController@receiveDispatchedProducts')->name('receive-dispatched-products');

		/* special routes */

		// packaging-packages
		Route::get('/api/packaging-packages/{perPage?}','AssetController@showAllPackagingPackages')->name('packaging-packages');
		
		Route::fallback(function () {

			$access_token = Illuminate\Support\Str::random(60);
        	// $roles = \Auth::guard('merchant')->user()->roles;
        	// $permissions = Auth::guard('merchant')->user()->permissions;
        	$generalSettings = \App\Models\ApplicationSetting::firstOrCreate([]);
			// return view('layouts.merchant', ['permissions' => $permissions, 'roles' => $roles, 'access_token' => $access_token, 'general_settings' => $generalSettings]);
			return view('layouts.merchant', ['access_token' => $access_token, 'general_settings' => $generalSettings]);
		    
		});

	});

});

Route::name('website.')->group(function () {

	Route::get('/{any?}', 'WebsiteController@index')->name('home');
	Route::post('/contact-queries', 'WebsiteController@storeNewMessage')->name('contact-queries');
	Route::post('/owners', 'WebsiteController@storeOwnerRegistrationRequest')->name('owners');
	Route::post('/merchants', 'WebsiteController@storeMerchantRegistrationRequest')->name('merchants');
	Route::post('/quotations', 'WebsiteController@submitQuotation')->name('quotations');
	Route::post('/jobs', 'WebsiteController@submitJobApplication')->name('jobs');

});