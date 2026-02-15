<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InstallMentController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\PayInstallmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransactionController;
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
    //stripe card store
    Route::get('card/setup-intent', [CardController::class, 'create']);
    Route::post('card/store', [CardController::class, 'store']);
    Route::post('send-payment', [PaymentController::class, 'sendPayment']);

    //installment show
    Route::get('installment', [InstallMentController::class, 'showInstallment']);
    //installment 
    Route::post('paylater/preview', [InstallMentController::class, 'preview']);

    // installment pay 
    Route::post('paylater/confirm', [InstallMentController::class, 'confirmPayment']);

    //installment show 
    Route::get('user/installments', [InstallMentController::class, 'userInstallments']);

    Route::post('installment/pay', [InstallMentController::class, 'InstallmentPayNow']);
    
    //pay single installment

    Route::post('installments/pay-single', [PayInstallmentController::class, 'SingleInstallment']);

    //show all installment pay Data

    Route::get('pending-all-installment/{installmentID}', [PayInstallmentController::class, 'ShowAllInstamentData']);

    Route::post('pay-all-installment',[PayInstallmentController::class, 'PayAllInstallment']);

    //show recent transaction
    Route::get('recent/transaction', [TransactionController::class, 'TransactionIndex']);
    //fetch Merchant
    Route::get('get-merchant/{phone}', [MerchantController::class, 'fetchMerchant']);
});