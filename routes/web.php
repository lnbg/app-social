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

Route::get('/', 'HomeController@index');
Route::get('/facebook/fanpage', 'HomeController@facebookFanpage');
Route::get('/facebook/profile', 'HomeController@facebookProfile');
Route::get('/facebook/analytics-fanpage', 'FacebookAnalyticsController@analyticsFacebookFanpage');

Route::post('/facebook/add-new-page', 'FacebookAnalyticsController@addNewFacebookPage');

Route::get('/setup', 'UserController@setup');
Route::get('/facebook/callback', 'UserController@facebookCallback');
