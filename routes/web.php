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

Route::get('/', 'PageController@index')->name('index');

Route::get('atis-map-data', 'FsdDataController@getClients')->name('atis-map-data');

Route::get('full_map', 'PageController@fullMap');

Route::get('details', 'DetailController@data');
Route::get('details/{frequency}', 'DetailController@freq');

