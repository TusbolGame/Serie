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

Route::get('/{name}', [
    'as' => 'home',
    'uses' => 'HomeController@index'
])->where('name', '(home)?')->middleware('auth')->name('home');

Auth::routes();

Route::post('/errors/javascript/add', 'JavascriptErrorController@errorManager')->middleware('auth');

Route::get('/data/update/{type}', 'DataUpdateController@updateManager')->middleware('auth');
Route::get('/episode/torrent/add/{episode}/{magnetlink}', 'EpisodeController@torrentAdd')->middleware('auth');
Route::get('/episode/torrent/check/{magnetlink}', 'EpisodeController@torrentCheck')->middleware('auth');
Route::get('/episode/view/mark/{episode}/{state}', 'EpisodeController@viewMark')->middleware('auth');
Route::get('/episode/action/add/{buttonType}', 'EpisodeController@actionAdd')->middleware('auth');

Route::get('/show/remove/{show}', 'ShowController@removeShow')->middleware('auth');
