<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'ScoreController@index');

Route::group(['_active_menu' => 'score'], function () {
    Route::post('/score', 'ScoreController@score');
    Route::get('/score', 'ScoreController@scoreForm');
});
Route::group(['_active_menu' => 'battle'], function () {
    Route::post('/battle', 'ScoreController@battle');
    Route::get('/battle', 'ScoreController@battleForm');
});
Route::group(['_active_menu' => 'all'], function () {
    Route::get('/all', 'ScoreController@getAll');
});
