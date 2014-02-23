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
Route::get("/login", "HomeController@login");

Route::get("/logout", "HomeController@logout");

Route::get("/newuser", "HomeController@newUser");

Route::get("/forbidden", "HomeController@forbidden");

Route::get("/topics/{subject}", "FileController@topics");

Route::get("/download/{year}/{category}/{subject}/{topic}", "FileController@download");

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

Route::get("/{year}", "HomeController@years");

Route::get("/{year}/{category}", "HomeController@categories");

Route::get("/{year}/{category}/{subject}", "HomeController@subjects");

Route::get("/{year}/{category}/{subject}/request", "HomeController@subjects2");

Route::get("/{year}/{category}/{subject}/topic/{topic}", "HomeController@topics");

Route::get("/{year}/{category}/{subject}/request/{request}", "HomeController@requests");