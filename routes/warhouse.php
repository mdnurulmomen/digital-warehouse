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

Route::name('warhouse.')->group(function () {

    Route::get('/', function () {
       return view('auth.login', ['url' => 'warhouse']); 
    })->name('login');

	Route::post('/', 'Auth\LoginController@warhouseLogin')->name('login');

	Route::get('/home', 'HomeController@warhouseHome')->name('home');

});
