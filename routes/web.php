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

Route::get('signup/{provider}', 'Auth\RegisterController@redirectToProvider');
Route::get('signup/{provider}/callback', 'Auth\RegisterController@handleProviderCallback');

Route::get('/dataset.js', 'IndexController@getDataSet')->name('dataset');
Route::get('/test', 'IndexController@test')->name('test');

Route::get('/video/wrong-url','IndexController@wrongUrl')->name('wrong.url');

Route::get('download/{hash}', 'AttachmentController@download')->name('attachment.download');

Route::get('/{any}', 'IndexController@index')->where(['any' => '.*']);
Route::get('/oauth', 'IndexController@index')->name('oauth');