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


Route::post('/setting/payment-bpartners', 'SettingController@paymentBpartners');
Route::any('/apix', 'AmoController@get_token');


Route::any('/intellect/start', 'IntellectController@start'); // Bitrix -> Admin -> Intellect
Route::any('/intellect/save', 'IntellectController@save');   // Intellect -> Admin -> Bitrix
Route::any('/intellect/get_name', 'IntellectController@get_name');   // Intellect -> Admin 
Route::any('/intellect/get_link', 'IntellectController@get_link');   // Intellect -> Admin 
Route::any('/intellect/get_time', 'IntellectController@get_time');   // Intellect -> Admin 
Route::any('/intellect/change_status', 'IntellectController@change_status');   // Intellect -> Admin 
Route::any('/intellect/send_message', 'IntellectController@send_message');   // Admin -> Intellect 
Route::any('/intellect/create_lead', 'IntellectController@create_lead');   // Admin -> Intellect 
Route::any('/intellect/test', 'IntellectController@test');   // Admin -> Intellect 
Route::any('/intellect/save_quiz_after_fire', 'IntellectController@quiz_after_fire');   // Intellect -> Admin 
Route::any('/intellect/save_estimate_trainer', 'HomeController@save_estimate_trainer');  


Route::any('/bitrix/new-lead', 'IntellectController@newLead');   // Bitrix -> Admin 
Route::any('/bitrix/edit-lead', 'IntellectController@editLead');   // Bitrix -> Admin 
Route::any('/bitrix/edit-deal', 'IntellectController@editDeal');   // Bitrix -> Admin 
Route::any('/bitrix/lose-deal', 'IntellectController@loseDeal');   // Bitrix -> Admin 
Route::any('/bitrix/create-link', 'IntellectController@bitrixCreateLead');   // Bitrix -> Admin 
Route::any('/bitrix/change-resp', 'IntellectController@changeResp');   // Bitrix -> Admin 
Route::any('/bitrix/inhouse', 'IntellectController@inhouse');   // Bitrix -> Admin 

