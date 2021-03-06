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
Route::middleware(['cache.headers:public;max_age=3600'])->group(function () {
    Route::get('/', 'PageController@index')->name('pages.index');
    Route::get('/search', 'PageController@search')->name('pages.search');
    Route::get('/articles', 'ArticleController@index')->name('articles.index');
    Route::get('/articles/search', 'ArticleController@search')->name('articles.search');

    Route::get('/logs/search', 'SearchLogController@index')->name('logs.search');
});
