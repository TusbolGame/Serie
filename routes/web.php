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

Route::get('/data/search/show/{show_name}', 'DataUpdateController@searchShowController')->middleware('auth');
Route::get('/data/update/{type}/{api_id?}', 'DataUpdateController@updateManager')->middleware('auth');

Route::get('/episode/{episode}', 'EpisodeController@episode')->middleware('auth');
Route::get('/episode/torrent/add/{episode}/{magnetlink}', 'EpisodeController@torrentAdd')->middleware('auth');
Route::get('/episode/torrent/check/{magnetlink}', 'EpisodeController@torrentCheck')->middleware('auth');
Route::get('/episode/view/mark/{episode}/{state}', 'EpisodeController@viewMark')->middleware('auth');
Route::get('/episode/action/add/{buttonType}', 'EpisodeController@actionAdd')->middleware('auth');

Route::get('/show/{show}', 'ShowController@show')->middleware('auth');
Route::get('/show/remove/{show}', 'ShowController@removeUserShow')->middleware('auth');
Route::get('/show/add/{show}', 'ShowController@addUserShow')->middleware('auth');





Route::get('/episode-action', function(){
//    $redis = app()->make(('redis'));
//    $redis->set('user', 'pinuccio');
//    $redis->get('user');
    $episode = \App\Episode::where('id', 1)->with('show')->first();
    $show = \App\Show::where('id', 1)->first();
    broadcast(new \App\Events\EpisodeCreated($episode));
    broadcast(new \App\Events\ShowUpdated($show, 5, 14));
});
