<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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


Auth::routes(['register' => false]);

Route::get('/', 'HomeController@index')->name('home');

#PersonalData
Route::get('/personalData/{user}', 'PersonalDataController@show')->name('personalData.show');
Route::get('/personalData/{user}/edit', 'PersonalDataController@edit')->name('personalData.edit');
Route::patch('/personalData/{user}', 'PersonalDataController@update')->name('personalData.update');

#Meeting
Route::get('/meeting/create', 'MeetingController@create')->name('meeting.create');
Route::get('/meeting/{meeting}', 'MeetingController@show')->name('meeting.show');
Route::patch('/meeting/{meeting}', 'MeetingController@update')->name('meeting.update');
Route::get('/meetings/', 'MeetingController@index')->name('meeting.index');
Route::get('/meetings/get', 'MeetingController@serveMeetings')->name('meeting.serveMeetings');
Route::post('/meeting', 'MeetingController@store')->name('meeting.store');

#FundsCenter
Route::get('/fundsCenter/create', 'FundsCenterController@create')->name('fundsCenter.create');
Route::get('/fundsCenter/{fundsCenter}', 'FundsCenterController@show')->name('fundsCenter.show');
Route::patch('/fundsCenter/{fundsCenter}', 'FundsCenterController@update')->name('fundsCenter.update');
Route::get('/fundsCenters/', 'FundsCenterController@index')->name('fundsCenter.index');
Route::get('/fundsCenters/get', 'FundsCenterController@serveFundCenters')->name('fundsCenter.serveFundsCenters');
Route::post('/fundsCenter', 'FundsCenterController@store')->name('fundsCenter.store');

#OneTimePayment
Route::get('/oneTimePayment/create', 'OneTimePaymentController@create')->name('oneTimePayment.create');
Route::get('/oneTimePayment/{oneTimePayment}', 'OneTimePaymentController@show')->name('oneTimePayment.show');
Route::patch('/oneTimePayment/{oneTimePayment}', 'OneTimePaymentController@update')->name('oneTimePayment.update');
Route::get('/oneTimePayments/', 'OneTimePaymentController@index')->name('oneTimePayment.index');
Route::get('/oneTimePayments/get', 'OneTimePaymentController@serveOneTimePayment')->name('oneTimePayment.serveOneTimePayment');
Route::get('/oneTimePayment/createTransferFormular/{oneTimePayment}', 'OneTimePaymentController@createTransferFormular')->name('oneTimePayment.createTransferFormular');
Route::get('/oneTimePayment/createBskFormular/{oneTimePayment}', 'OneTimePaymentController@createBskFormular')->name('oneTimePayment.createBskFormular');
Route::post('/oneTimePayment', 'OneTimePaymentController@store')->name('oneTimePayment.store');

#OngoingPayment
Route::get('/ongoingPayment/create', 'OngoingPaymentController@create')->name('ongoingPayment.create');
Route::get('/ongoingPayment/createTransferFormular', 'OngoingPaymentController@createTransferFormular')->name('ongoingPayment.createTransferFormular');
Route::get('/ongoingPayment/createBskFormular', 'OngoingPaymentController@createBskFormular')->name('ongoingPayment.createBskFormular');
Route::get('/ongoingPayment/{ongoingPayment}', 'OngoingPaymentController@show')->name('ongoingPayment.show');
Route::patch('/ongoingPayment/{ongoingPayment}', 'OngoingPaymentController@update')->name('ongoingPayment.update');
Route::get('/ongoingPayments/', 'OngoingPaymentController@index')->name('ongoingPayment.index');
Route::get('/ongoingPayments/get', 'OngoingPaymentController@serveOngoingPayment')->name('ongoingPayment.serveOngoingPayment');
Route::post('/ongoingPayment', 'OngoingPaymentController@store')->name('ongoingPayment.store');


#OngoingPaymentHistory
Route::patch('/ongoingPaymentHistory/{ongoingPaymentHistory}', 'OngoingPaymentHistoryController@update')->name('ongoingPaymentHistory.update');
Route::get('/ongoingPaymentHistories/get', 'OngoingPaymentHistoryController@serveOngoingPaymentHistories')->name('ongoingPaymentHistory.serveOngoingPaymentHistories');

#SqmPayment
Route::get('/sqmPayment/create', 'SqmPaymentController@create')->name('sqmPayment.create');
Route::get('/sqmPayment/{sqmPayment}', 'SqmPaymentController@show')->name('sqmPayment.show');
Route::patch('/sqmPayment/{sqmPayment}', 'SqmPaymentController@update')->name('sqmPayment.update');
Route::get('/sqmPayments/', 'SqmPaymentController@index')->name('sqmPayment.index');
Route::get('/sqmPayments/get', 'SqmPaymentController@serveSqmPayments')->name('sqmPayment.serveSqmPayments');
Route::post('/sqmPayment', 'SqmPaymentController@store')->name('sqmPayment.store');

#Claim
Route::get('/claim/{claim}', 'ClaimController@show')->name('claim.show');
Route::patch('/claim/{claim}', 'ClaimController@update')->name('claim.update');
Route::get('/claims/get', 'ClaimController@serveClaims')->name('claim.serveClaims');
Route::post('/claim', 'ClaimController@store')->name('claim.store');
Route::get('/claims/showDocument', 'ClaimController@showDocument')->name('claim.showDocument');

#CostType
Route::get('/costTypes/get', 'CostTypeController@serveCostTypes')->name('costType.serveCostTypes');

#Planning
Route::get('/planning', 'PlanningController@index')->name('planning.index');

Route::get('/test', 'PdfEditor@save')->name('test.test');
