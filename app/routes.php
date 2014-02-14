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

// Static Routes
Route::get("/logout", "HomeController@logout");

Route::get("/newuser", "HomeController@newUser");

Route::get("/forbidden", "HomeController@forbidden");

Route::get("/teemo", function()
{
	return View::make("teemo");
});

Route::get("/test", function()
{
	return View::make("test.index");
});

Route::get("/test/view", function()
{
	return View::make("test.view");
});

// Main Routes
Route::get("/", "HomeController@index");

Route::get("/{year}", "HomeController@categories");

Route::get("/{year}/{category}", "HomeController@subjects");