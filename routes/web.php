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
    Route::get('/admin/event/{event}/refresh', 'Admin\EventController@refresh');
    Route::post('/admin/event/create', 'Admin\EventController@create');

    Route::get('/admin/export/{export}', 'Admin\ExportController@show');

    Route::get('/admin/node/{node}', 'Admin\NodeController@show');
    Route::post('/admin/node/{node}/update-qp', 'Admin\NodeController@updateQp');

    Route::post('/admin/parser/fix-unknown', 'Admin\ParserController@fixUnknown');
    Route::get('/admin/parser/parse-failed', 'Admin\ParserController@parseFailed');
    Route::get('/admin/parser/remove-failed', 'Admin\ParserController@removeFailed');
    Route::get('/admin/parser/start-match', 'Admin\ParserController@startMatch');

    Route::get('/admin/submission/search', 'Admin\SubmissionController@search');
    Route::get('/admin/submission/{submission}', 'Admin\SubmissionController@show');
    Route::get('/admin/submission/{submission}/reparse', 'Admin\SubmissionController@reparse');
    Route::get('/admin/submission/{submission}/remove', 'Admin\SubmissionController@remove');
    Route::post('/admin/submission/create', 'Admin\SubmissionController@create');
    Route::post('/admin/submission/{submission}/override-drop-count', 'Admin\SubmissionController@overrideDropCount');
    Route::post('/admin/submission/{submission}/override-drop-stack', 'Admin\SubmissionController@overrideDropStack');
    Route::post('/admin/submission/{submission}/override-qp-total', 'Admin\SubmissionController@overrideQpTotal');
    Route::post('/admin/submission/{submission}/override-scroll-position', 'Admin\SubmissionController@overrideScrollPosition');

    Route::get('/admin/template/{code}', 'Admin\TemplateController@show');
    Route::post('/admin/template/create', 'Admin\TemplateController@create');
    Route::post('/admin/template/remove', 'Admin\TemplateController@remove');
});
