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
Route::get('/facebook/get-facebook-page-analytics', 'FacebookAnalyticsController@getFacebookPageByFacebookAnalyticID');

Route::post('/facebook/analytics-facebook-page', 'FacebookAnalyticsController@analyticsFacebookPage');
Route::post('/facebook/create-new-facebook-page', 'FacebookAnalyticsController@createNewFacebookPage');

Route::get('/instagram/get-list-instagram-profile-analytics', 'InstagramAnalyticsController@getListInstagramProfilesAnalytics');
Route::get('/instagram/get-instagram-profile-analytics', 'InstagramAnalyticsController@getInstagramProfileByInstagramAnalyticsID');
Route::post('/instagram/analytics-instagram-profile', 'InstagramAnalyticsController@analyticsInstagramProfile');
Route::post('/instagram/create-new-instagram-profile', 'InstagramAnalyticsController@createNewInstagramProfile');
// Debug
Route::get('/debug', 'FacebookAnalyticsController@debug');
Route::get('/instagram/debug', 'InstagramAnalyticsController@debug');