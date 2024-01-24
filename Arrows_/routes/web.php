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

Route::group(['middleware' => ['auth']], function(){
    Route::get('/', 'HomeController@index');

    Route::get('/rooms', 'RoomController@getRoomList');

    Route::get('/message/{id}', 'MessageController@get');
    Route::post('/message', 'MessageController@store');

    Route::get('/account', 'UserController@index');
    Route::post('/account/update', 'UserController@update');
    Route::post('/account/password/update', 'UserController@updatePassword');
    
    Route::get('/friend', 'FriendController@index');
    Route::post('/friend', 'FriendController@store');
    Route::get('/friend/search', 'FriendController@search');
});