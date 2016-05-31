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


Route::get('/', 'UserScoreController@index');

Route::post('/score', 'UserScoreController@score');

//Route::get('/battle/{username1}/{username2}', 'UserScoreController@battle');
