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

Route::get('/', 'HomeController@index');
Route::post('/submit', 'SubmissionController@submit');

Route::middleware('auth.basic')->group(function () {
    Route::get('/admin', 'AdminController@index');
    Route::post('/admin/create-event', 'AdminController@createEvent');
});
