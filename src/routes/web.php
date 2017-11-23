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

Route::get('/', 'SearchController@index')->name('index');
Route::get('/search', 'SearchController@search')->name('search');
Route::get('/sites',  'SiteController@index'   )->name('sites');

// basic認証つける
Route::group(['middleware' => 'auth.basic'], function () {
  Route::get   ('/user',       'UserController@index'   )->name('user.index');
  Route::post  ('/user',       'UserController@store'   )->name('user.store');
  Route::put   ('/user/{id}',  'UserController@update'  )->name('user.update');
  Route::delete('/user/{id}',  'UserController@destroy' )->name('user.destroy');

  Route::get   ('/rss',       'RssController@index'   )->name('rss.index');
  Route::post  ('/rss',       'RssController@store'   )->name('rss.store');
  Route::put   ('/rss/{id}',  'RssController@update'  )->name('rss.update');
  Route::delete('/rss/{id}',  'RssController@destroy' )->name('rss.destroy');
});
