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
Route::get('/spapitest','viewPageController@spapitest')->name('spapi');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/view','SaveAsinController@index')->name('show');
Route::get('/showInput','SaveAsinController@show')->name(('showInput'));

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');