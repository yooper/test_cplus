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
/*
Route::get('/', function () {
    return view('welcome');
});
 * 
 */

Route::get('/{filter?}', 'CalendarController@index')->name('events.index');
Route::get('/{id}', 'CalendarController@get')->name('events.index');
Route::post('/', 'CalendarController@addEvent')->name('events.add');

