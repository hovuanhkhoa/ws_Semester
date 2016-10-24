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


Route::get('/departure-airports/{id?}','APIController@indexDeparture');
Route::get('/arrival-airports','APIController@indexArrivalAirport');


Route::get('/bookings/{id?}','APIController@indexBooking');
Route::put('/bookings','APIController@updateBooking');
Route::post('/bookings','APIController@storeBooking');

Route::get('/bookings/{id?}/flight-details','APIController@indexFlightDetail');
Route::put('/bookings/{id?}/flight-details','APIController@updateFlightDetail');

Route::get('/bookings/{id?}/passengers','APIController@indexPassengerDetail');
Route::put('/bookings/{id?}/passengers','APIController@updatePassengerDetail');

Route::get('/flights','APIController@indexFlightSearch');

Route::get('/fare-types','APIController@indexFareType');

Route::get('/classes','APIController@indexClass');

/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
*/