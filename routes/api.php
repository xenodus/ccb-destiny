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