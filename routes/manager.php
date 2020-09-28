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

Route::name('manager.')->group(function () {

    Route::middleware(['guest:manager'])->group(function () {
	    
	    Route::get('/', function () {
	       return view('auth.login', ['url' => 'manager']); 
	    })->name('login');

		Route::post('/', 'Auth\LoginController@managerLogin')->name('login');

	});

	Route::get('/home', 'HomeController@managerHome')->name('home');

});
