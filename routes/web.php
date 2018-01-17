<?php

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

Route::get('/', 'ReportingController@getDashboard');

Route::get('/api/currently-tracking', 'APIController@getCurrentlyTracking');
Route::get('/api/totals/{range}', 'APIController@getTotals');
