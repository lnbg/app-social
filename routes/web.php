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

Route::get('/', 'AppController@index');


Route::get('/facebook/get-list-facebook-page-analytics', 'FacebookAnalyticsController@getListFacebookPageAnalytics');


Route::post('/facebook/analytics_facebook_page', 'FacebookAnalyticsController@analyticsFacebookPage');


// Debug
Route::get('/debug', 'FacebookAnalyticsController@debug');