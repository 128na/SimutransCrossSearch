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

Route::get('/', 'FrontController@index')->name('index');
Route::get('/search', 'FrontController@search')->name('search');

Route::get('/sitemap', 'SitemapController@index')->name('sitemap');
Route::get('/logs/schedule', 'ScheduleLogController@index')->name('logs.schedule');
Route::get('/logs/search', 'SearchLogController@index')->name('logs.search');
