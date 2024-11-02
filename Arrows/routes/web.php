<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;

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


Route::get('/friend/add/{username}', [FriendController::class, 'addMe']);

Route::group(['middleware' => ['auth']], function(){

    Route::get('/', function () {
        return view('/home');
    });

    Route::get('/rooms', [RoomController::class, 'getRoomList']);

    Route::get('/message/{id}', [MessageController::class, 'get']);
    Route::post('/message', [MessageController::class, 'store']);
    Route::post('/message/{id}', [MessageController::class, 'update']);
    Route::delete('/message/{id}', [MessageController::class, 'delete']);

    Route::get('/account', [UserController::class, 'index']);
    Route::post('/account/update', [UserController::class, 'update']);
    Route::post('/account/password/update', [UserController::class, 'updatePassword']);

    Route::get('/friend', [FriendController::class, 'index']);
    Route::post('/friend', [FriendController::class, 'store']);
    Route::get('/friend/search', [FriendController::class, 'search']);

});