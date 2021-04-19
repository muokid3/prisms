<?php

use Illuminate\Http\Request;
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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group([
    'namespace' => 'API'
], function() {

    Route::post('sms/receive', 'SmsController@receive');
    Route::post('sms/delivery_report', 'SmsController@delivery_report');
    Route::get('sms/randomise', 'SmsController@randomise');


});

Route::group([
    'prefix' => 'auth',
    'namespace' => 'API'
], function () {
    Route::post('login', 'AuthController@login');

//    Route::post('send_reset_otp', 'AuthController@send_reset_otp');
//    Route::post('verify_otp', 'AuthController@verify_otp');
//    Route::post('reset_pin', 'AuthController@reset_pin');
});

Route::group([
    'middleware' => [
        'auth:api',
    ],
    'namespace' => 'API'
], function() {
    Route::get('auth/logout', 'AuthController@logout');
    Route::get('auth/user', 'AuthController@user');







});





