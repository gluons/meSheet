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

/**
 * Static Routes
 */
Route::get("/login", "HomeController@login");

Route::get("/logout", "HomeController@logout");

Route::get("/newuser", "HomeController@newUser");

Route::get("/forbidden", "HomeController@forbidden");

Route::get("/topics/{subject}", "FileController@topics");

Route::get("/requests/{subject}", "RequestController@requests");

Route::get("/download/{year}/{category}/{subject}/{topic}", "FileController@download");

// Admin section

Route::get("/adm/categories", "AdminController@categories");

Route::get("/adm/categories/list", "AdminController@categoryList");

Route::get("/adm/subjects", "AdminController@subjects");

Route::get("/adm/subjects/list", "AdminController@subjectsList");


Route::post("/adm/categories/update", "AdminController@categoryUpdate");

Route::post("/adm/categories/remove", "AdminController@categoryRemove");

Route::post("/adm/subjects/update", "AdminController@subjectsUpdate");

Route::post("/adm/subjects/remove", "AdminController@subjectsRemove");

// Other section

Route::get("/teemo", function()
{
	return View::make("teemo");
});

/*
 * Main Routes
 */
Route::get("/", "HomeController@index");

Route::get("/{year}", "HomeController@years");

Route::get("/{year}/{category}", "HomeController@categories");

Route::get("/{year}/{category}/{subject}", "HomeController@subjects");

Route::get("/{year}/{category}/{subject}/topic", "HomeController@subjects");

Route::get("/{year}/{category}/{subject}/request", "HomeController@subjects2");

Route::get("/{year}/{category}/{subject}/topic/{topic}", "HomeController@topics");

Route::get("/{year}/{category}/{subject}/request/{request}", "HomeController@requests");


Route::post("/{year}/{category}/{subject}/topic/", "FileController@newTopic");

Route::post("/{year}/{category}/{subject}/request/", "RequestController@newRequest");