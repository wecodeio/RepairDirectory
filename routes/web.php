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

Route::get('/', 'MapController@index')->name('map.index');

Route::prefix('admin')
    ->namespace('Admin')
    ->group(function () {
        Route::get('/', 'AdminController@index')->name('admin.index');

        Route::get('business/{id?}', 'BusinessController@edit')->name('admin.business.edit');
        Route::post('business', 'BusinessController@create')->name('admin.business.create');
        Route::put('business/{id}', 'BusinessController@update')->name('admin.business.update');
    });

