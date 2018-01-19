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

Route::get('/search', 'ReportingController@getSearch');
Route::get('/run', 'ReportingController@getRun');

Route::get('/report/range/{from}/{to}', 'ReportingController@getRangeReport');
Route::get('/report/month', 'ReportingController@getMonthlyReport');
Route::get('/report/week', 'ReportingController@getWeeklyReport');
