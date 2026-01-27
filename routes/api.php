<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\MerchantController;
use Illuminate\Support\Facades\Route;
//user Account
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Merchant Account
Route::post('register/merchant', [MerchantController::class, 'register']);
Route::post('login/merchant', [MerchantController::class, 'login']);
Route::get('document', [DocumentController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

});