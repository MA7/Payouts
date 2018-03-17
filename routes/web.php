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
Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/settlements', 'HomeController@settlementsList')->name('list');
Route::get('/settlementShow/{id}', 'HomeController@settlementShow')->name('settlementShow');
Route::get('/settlements/add', 'HomeController@addSettlement')->name('add');
Route::get('/settlements/inquiry', 'HomeController@inquirySettlement')->name('inquiry');

Route::post('/settlements/register', 'HomeController@registerNewUser')->name('register.user');
Route::post('/settlements/create', 'HomeController@createRequest')->name('settlement.create');

// Ajax request.
Route::get('/settlements/checkMobile.json', 'HomeController@getCheckMobile')->name('getCheckMobile');
Route::get('/settlements/inquiry', 'HomeController@postCheckInquiry')->name('postCheckInquiry');


Route::group(['prefix'=>'user'],function (){
    Route::get('list', 'HomeController@userList')->name('user.list');
    Route::get('status/{id}', 'HomeController@userStatus')->name('user.status');
    Route::get('new', 'HomeController@newUser')->name('user.new');
    Route::post('new', 'HomeController@newUser')->name('user.new');
    Route::get('changepas/{id}', 'HomeController@userChangePassword')->name('user.changepas');
    Route::post('changepas/{id}', 'HomeController@userChangePassword')->name('user.changepas');
});
