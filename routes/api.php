<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::get('/vendor/{vendor_id?}', 'ApiController@get_vendor')->name('get_vendor');
Route::get('/nightfall', 'ApiController@get_nightfall')->name('get_nightfall');
Route::get('/sales-item-perks/{vendor_id}', 'ApiController@get_sales_item_perks')->name('get_sales_item_perks');
Route::get('/xur', 'ApiController@get_xur_location')->name('get_xur_location ');
Route::get('/levi', 'ApiController@get_leviathan_rotation')->name('get_leviathan_rotation ');
Route::get('/activity', 'ApiController@activity')->name('activity');
Route::get('/mtg/top_decks', 'ApiController@get_mtg_top_decks')->name('top_decks');

Route::get('/news/get/{category?}', 'ApiController@get_news')->name('get_news');
Route::get('/news/update', 'ApiController@update_news')->name('update_news');

Route::get('/get_membership_id/{bnet_id}', 'ApiController@get_membership_id_from_bnet_id')->name('get_membership_id_from_bnet_id');

Route::post('/pvp/closest_glory', 'ApiController@closest_glory')->name('closest_glory');
Route::post('/pvp/update_glory_from_db', 'ApiController@update_glory_from_db')->name('update_glory_from_db');