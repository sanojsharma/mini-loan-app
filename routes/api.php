<?php

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

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\TransactionController;

Route::group(['prefix' => 'v1'], function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/logout', [UserController::class, 'logout']);
        Route::post('/loans/create', [LoanController::class, 'store']);
        Route::post('/loans', [LoanController::class, 'index']);
        Route::post('/transaction/create', [TransactionController::class, 'store']);
        Route::get('/transaction', [TransactionController::class, 'index']);
    });

    Route::group(['middleware' => ['auth:api','scopes:approve-loan']], function () {
        Route::post('/admin/approve', [AdminController::class, 'approveLoanRequest']);
    });

});



