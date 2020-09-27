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

    Route::get('/', function () {
       return view('auth.login'); 
    })->name('login_page');

	Route::post('/', 'LoginController@adminLogin')->name('submit_login');

	Route::get('/home', 'HomeController@index')->name('home');

});


