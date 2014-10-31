<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', array(
	'as' => 'home',
	'uses' => 'HomeController@home'
));

Route::get('/getOldSale', array(
	'as' => 'getOldSale',
	'uses' => 'HomeController@getOldSale'
));

Route::get('/getCurrentSale', array(
	'as' => 'getCurrentSale',
	'uses' => 'HomeController@getCurrentSale'
));

Route::get('/skins/history', array(
	'as' => 'skinHistory',
	'uses' => 'SkinController@skinHistory'
));

Route::get('/champions/history', array(
	'as' => 'championHistory',
	'uses' => 'ChampionController@championHistory'
));