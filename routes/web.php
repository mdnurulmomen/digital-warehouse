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

		// profile
		Route::get('/api/profile', 'ProfileController@showMerchantProfile')->name('profile');	
		Route::put('/profile', 'ProfileController@updateMerchantProfile')->name('profile');	
		Route::post('/password', 'ProfileController@updateMerchantPassword')->name('password');
		
		/* complementary routes for merchants (my-properties) */
		// my-deals, spaces & payments
		Route::get('/api/my-space-deals/{perPage?}', 'MerchantController@showMyAllSpaceDeals')->name('my-space-deals.index');
		Route::post('/search-my-space-deals/{perPage}', 'MerchantController@searchMyAllSpaceDeals')->name('search-my-space-deals');

		// deal-rents
		Route::get('/api/my-space-deal/{deal}/rents/{perPage?}', 'MerchantController@showMySpaceDealAllRents')->name('my-space-rents.index');
		Route::post('/search-my-space-deal-rents/{perPage}', 'MerchantController@searchMySpaceDealAllRents')->name('search-my-space-rents');

		// my-products
		Route::get('/api/my-products/{perPage?}', 'MerchantController@showMyAllProducts')->name('my-products.index');
		Route::post('/search-my-products/{perPage}', 'MerchantController@searchMyAllProducts')->name('search-my-products');

		// product-stock
		Route::get('/api/my-product-stocks/{productMerchant}/{perPage?}', 'MerchantController@showMyProductAllStocks')->name('my-product-stocks.index');
		Route::post('/api/search-my-product-stocks/{productMerchant}/{perPage}', 'MerchantController@searchMyProductAllStocks')->name('search-my-product-stocks');

		// my-requisitions
		Route::get('/api/my-requisitions/{perPage?}', 'MerchantController@showMyAllRequisitions')->name('my-requisitions.index');
		Route::post('/requisitions/{perPage}', 'MerchantController@makeNewRequisition')->name('my-requisitions.create');
		Route::post('/api/search-my-requisitions/{perPage}', 'MerchantController@searchMyAllRequisitions')->name('search-my-requisitions');

		// my-agents
		Route::get('/api/my-agents/{perPage?}', 'MerchantController@showMyAllAgents')->name('my-agents.index');
		
		// Receive products
		Route::post('/receive-dispatched-products/{perPage}', 'MerchantController@receiveDispatchedProducts')->name('receive-dispatched-products');

		/* public routes */
		// packaging-packages
		Route::get('/api/packaging-packages/{perPage?}','AssetController@showAllPackagingPackages')->name('packaging-packages.index');
		Route::get('/api/search-packaging-packages/{search}/{perPage}','AssetController@searchAllPackagingPackages')->name('search-packaging-packages');

		// delivery-companies
		Route::get('/api/delivery-companies/{perPage?}','AssetController@showDeliveryAllCompanies')->name('delivery-companies.index');
		Route::get('/api/search-delivery-companies/{search}/{perPage}','AssetController@searchDeliveryAllCompanies')->name('search-delivery-companies');
		
		Route::fallback(function () {
			$currentMerchant = \Auth::user();
	        $generalSettings = \App\Models\ApplicationSetting::firstOrCreate([]);

	        return view('layouts.merchant', [/*'permissions' => $permissions, 'roles' => $roles, 'access_token' => $access_token,*/ 'merchant' => $currentMerchant, 'general_settings' => $generalSettings]);
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