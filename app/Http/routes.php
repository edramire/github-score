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

Route::post('/score', 'ScoreController@score');
Route::get('/score', 'ScoreController@score_form');

Route::post('/battle', 'ScoreController@battle');
Route::get('/battle', 'ScoreController@battle_form');

Route::get('/all', 'ScoreController@getAll');