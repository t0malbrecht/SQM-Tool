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


Auth::routes(['register' => false, 'reset' => false]);

Route::get('/', 'HomeController@index')->name('home');

#PersonalData
Route::get('/personalData/{personalData}', 'PersonalDataController@show')->name('personalData.show')->middleware('can:view,personalData');
Route::get('/personalData/{personalData}/edit', 'PersonalDataController@edit')->name('personalData.edit')->middleware('can:update,personalData');
Route::patch('/personalData/{personalData}', 'PersonalDataController@update')->name('personalData.update')->middleware('can:update,personalData');

#Meeting
Route::get('/meeting/create', 'MeetingController@create')->name('meeting.create');
Route::get('/meeting/{meeting}', 'MeetingController@show')->name('meeting.show');
Route::get('/meeting/exportXlsx/{meeting}', 'MeetingController@exportXlsx')->name('meeting.exportXlsx');
Route::delete('/meeting/{meeting}', 'MeetingController@delete')->name('meeting.delete');
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
Route::delete('/oneTimePayment/{oneTimePayment}', 'OneTimePaymentController@delete')->name('oneTimePayment.delete');
Route::patch('/oneTimePayment/{oneTimePayment}', 'OneTimePaymentController@update')->name('oneTimePayment.update');
Route::get('/oneTimePayments/', 'OneTimePaymentController@index')->name('oneTimePayment.index');
Route::get('/oneTimePayments/get', 'OneTimePaymentController@serveOneTimePayment')->name('oneTimePayment.serveOneTimePayment');
Route::get('/oneTimePayment/createTransferFormular/{oneTimePayment}', 'OneTimePaymentController@createTransferFormular')->name('oneTimePayment.createTransferFormular');
Route::get('/oneTimePayment/createBskFormular/{oneTimePayment}', 'OneTimePaymentController@createBskFormular')->name('oneTimePayment.createBskFormular');
Route::get('/oneTimePayment/createVnFormular/{oneTimePayment}', 'OneTimePaymentController@createVnFormular')->name('oneTimePayment.createVnFormular');
Route::post('/oneTimePayment', 'OneTimePaymentController@store')->name('oneTimePayment.store');

#OngoingPayment
Route::get('/ongoingPayment/create', 'OngoingPaymentController@create')->name('ongoingPayment.create');
Route::get('/ongoingPayment/createTransferFormular', 'OngoingPaymentController@createTransferFormular')->name('ongoingPayment.createTransferFormular');
Route::get('/ongoingPayment/createBskFormular', 'OngoingPaymentController@createBskFormular')->name('ongoingPayment.createBskFormular');
Route::get('/ongoingPayment/createVnFormular/{ongoingPayment}', 'OngoingPaymentController@createVnFormular')->name('ongoingPayment.createVnFormular');
Route::get('/ongoingPayment/{ongoingPayment}', 'OngoingPaymentController@show')->name('ongoingPayment.show');
Route::delete('/ongoingPayment/{ongoingPayment}', 'OngoingPaymentController@delete')->name('ongoingPayment.delete');
Route::patch('/ongoingPayment/{ongoingPayment}', 'OngoingPaymentController@update')->name('ongoingPayment.update');
Route::get('/ongoingPayments/', 'OngoingPaymentController@index')->name('ongoingPayment.index');
Route::get('/ongoingPayments/get', 'OngoingPaymentController@serveOngoingPayment')->name('ongoingPayment.serveOngoingPayment');
Route::post('/ongoingPayment', 'OngoingPaymentController@store')->name('ongoingPayment.store');


#OngoingPaymentHistory
Route::patch('/ongoingPaymentHistory/{ongoingPaymentHistory}', 'OngoingPaymentHistoryController@update')->name('ongoingPaymentHistory.update');
Route::get('/ongoingPaymentHistories/get', 'OngoingPaymentHistoryController@serveOngoingPaymentHistories')->name('ongoingPaymentHistory.serveOngoingPaymentHistories');
Route::delete('/ongoingPaymentHistory/{ongoingPaymentHistory}', 'OngoingPaymentHistoryController@delete')->name('ongoingPaymentHistory.delete');

#SqmPayment
Route::get('/sqmPayment/create', 'SqmPaymentController@create')->name('sqmPayment.create');
Route::get('/sqmPayment/{sqmPayment}', 'SqmPaymentController@show')->name('sqmPayment.show');
Route::delete('/sqmPayment/{sqmPayment}', 'SqmPaymentController@delete')->name('sqmPayment.delete');
Route::patch('/sqmPayment/{sqmPayment}', 'SqmPaymentController@update')->name('sqmPayment.update');
Route::get('/sqmPayments/', 'SqmPaymentController@index')->name('sqmPayment.index');
Route::get('/sqmPayments/get', 'SqmPaymentController@serveSqmPayments')->name('sqmPayment.serveSqmPayments');
Route::post('/sqmPayment', 'SqmPaymentController@store')->name('sqmPayment.store');

#Claim
Route::delete('/claim/{claim}', 'ClaimController@delete')->name('claim.delete');
Route::patch('/claim/{claim}', 'ClaimController@update')->name('claim.update');
Route::get('/claims/get', 'ClaimController@serveClaims')->name('claim.serveClaims');
Route::post('/claim', 'ClaimController@store')->name('claim.store');
Route::get('/claims/showDocument', 'ClaimController@showDocument')->name('claim.showDocument');

#ProofOfUse
Route::get('/proofOfUse/{proofOfUse}', 'ProofOfUseController@show')->name('proofOfUse.show');
Route::delete('/proofOfUse/{proofOfUse}', 'ProofOfUseController@delete')->name('proofOfUse.delete');
Route::patch('/proofOfUse/{proofOfUse}', 'ProofOfUseController@update')->name('proofOfUse.update');
Route::get('/proofOfUses/get', 'ProofOfUseController@serveProofOfUses')->name('proofOfUse.serveProofOfUses');
Route::post('/proofOfUse', 'ProofOfUseController@store')->name('proofOfUse.store');
Route::get('/proofOfUses/showDocument', 'ProofOfUseController@showDocument')->name('proofOfUse.showDocument');

#User
Route::delete('/user/{user}', 'UserController@delete')->name('user.delete')->middleware('can:deleteAny');
Route::patch('/user/{user}', 'UserController@update')->name('user.update')->middleware('can:updateAny');
Route::get('/users/get', 'UserController@serveUsers')->name('user.serveUsers')->middleware('can:viewAny');
Route::post('/user', 'UserController@store')->name('user.store')->middleware('can:createAny');
Route::get('/users/', 'UserController@index')->name('user.index')->middleware('can:viewAny');

#CostType
Route::get('/costTypes/get', 'CostTypeController@serveCostTypes')->name('costType.serveCostTypes');

#Planning
Route::get('/planning', 'PlanningController@index')->name('planning.index');

Route::get('/test', 'PdfEditor@save')->name('test.test');
