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

// Main Routes
Route::get('/', 'HomeController@index');

Route::get('/-body', 'HomeController@indexBody');

Route::get('/categories', 'HomeController@categories');

Route::get('/categories-body', 'HomeController@categoriesBody');

// Other Routes
Route::get('/teemo', function()
{
	return View::make('teemo');
});

Route::get('/test', function()
{
	return View::make('test.index');
});

Route::get('/test/view', function()
{
	return View::make('test.view');
});