<?php

use Illuminate\Support\Facades\Route;

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

Route::any('/webhook', 'App\\WebhookController@listen');
Route::any('/webhook_tcb', 'App\\WebhookController@listenTcb');
Route::any('/webhook_android', 'App\\WebhookController@listenAndroid');
Route::any('/whitelist_account_numbers', 'App\\WebhookController@getWhitelistAccountNumbers');
Route::get('/bank_transactions', 'App\\WebhookController@getBankTransactions');
Route::get('/orders', 'Api\\OrderController@index');
Route::get('/customers', 'App\\WebhookController@getCustomers');

Route::get('/test', 'Api\\TestController@index');

