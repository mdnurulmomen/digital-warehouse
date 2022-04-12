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
		
		Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

		/* Special Routes */
		/**/

		Route::fallback(function () {

			$access_token = Illuminate\Support\Str::random(60);
        	// $roles = \Auth::guard('owner')->user()->roles;
        	// $permissions = Auth::guard('owner')->user()->permissions;
        	$generalSettings = \App\Models\ApplicationSetting::firstOrCreate([]);
			// return view('layouts.owner', ['permissions' => $permissions, 'roles' => $roles, 'access_token' => $access_token, 'general_settings' => $generalSettings]);
			return view('layouts.owner', ['access_token' => $access_token, 'general_settings' => $generalSettings]);
		    
		});
	
	});

});
