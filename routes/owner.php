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

	Route::get('/home', 'HomeController@ownerHome')->name('home');

});
