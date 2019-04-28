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

Route::get('/atis-map', 'PageController@atisMap')->name('atis-map');
Route::get('/atis-map-data', 'PageController@atisMapData')->name('atis-map-data');
