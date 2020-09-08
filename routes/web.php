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
    Route::get('/admin', 'Admin\AdminController@index');

    Route::get('/admin/events', 'Admin\EventController@index');
    Route::get('/admin/event/{event}', 'Admin\EventController@show');
    Route::post('/admin/event/create', 'Admin\EventController@create');

    Route::get('/admin/node/{node}', 'Admin\NodeController@show');

    Route::post('/admin/parser/fix-unknown', 'Admin\ParserController@fixUnknown');

    Route::get('/admin/submission/search', 'Admin\SubmissionController@search');
    Route::get('/admin/submission/{submission}', 'Admin\SubmissionController@show');
    Route::post('/admin/submission/create', 'Admin\SubmissionController@create');
});
