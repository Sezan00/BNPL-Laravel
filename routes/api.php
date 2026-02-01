<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
//user Account
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Merchant Account
Route::post('register/merchant', [MerchantController::class, 'register']);
Route::post('login/merchant', [MerchantController::class, 'login']);
Route::get('document', [DocumentController::class, 'index']);

Route::middleware('auth:merchant')->post('merchant/logout', [MerchantController::class, 'merchantLogout']);


Route::middleware('auth:sanctum')->group(function () {
    //fetching user data to user dashboard
    Route::get('/user-data', [AuthController::class, 'index']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('card/setup-intent', [CardController::class, 'create']);
    Route::post('card/store', [CardController::class, 'store']);
    Route::post('send-payment', [PaymentController::class, 'sendPayment']);
});