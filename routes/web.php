<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CreditCardController;
use App\Http\Controllers\ExternalTransferController;
use App\Http\Controllers\InternalTransferController;
use App\Http\Controllers\InvestmentMovementController;
use App\Http\Controllers\SeparateMovementController;
use App\Http\Controllers\PaymentServiceController;
use App\Http\Controllers\PaymentCcController;
use App\Http\Controllers\UserController;

Route::prefix('api')->withoutMiddleware('web')->group(function () {
    // Rutas para Client
    Route::get('/clients', [ClientController::class, 'index']);
    Route::get('/clients/{id}', [ClientController::class, 'show']);
    Route::post('/clients', [ClientController::class, 'store']);
    Route::put('/clients/{id}', [ClientController::class, 'update']);
    Route::delete('/clients/{id}', [ClientController::class, 'destroy']);

    // Rutas para Account
    Route::get('/clients/{clientId}/accounts', [AccountController::class, 'getClientAccounts']);

    //NUEVO
    Route::post('/accounts', [AccountController::class, 'store']);
    Route::put('/accounts/{id}', [AccountController::class, 'update']);
    Route::delete('/accounts/{id}', [AccountController::class, 'destroy']);


    // Rutas para Credit Cards
    Route::get('/clients/{clientId}/credit_cards', [CreditCardController::class, 'index']);
    Route::get('/clients/{clientId}/credit_cards/{id}', [CreditCardController::class, 'show']);
    Route::post('/credit-cards', [CreditCardController::class, 'store']);
    Route::put('/credit-cards/{id}', [CreditCardController::class, 'update']);
    Route::delete('/credit-cards/{id}', [CreditCardController::class, 'destroy']);

    // Rutas para External Transfers
    Route::get('/accounts/{accountId}/external_transfers', [ExternalTransferController::class, 'index']);
    Route::get('/accounts/{accountId}/external_transfers/{id}', [ExternalTransferController::class, 'show']);
    Route::post('/external-transfers', [ExternalTransferController::class, 'store']);
    Route::put('/external-transfers/{id}', [ExternalTransferController::class, 'update']);
    Route::delete('/external-transfers/{id}', [ExternalTransferController::class, 'destroy']);

    // Rutas para Internal Transfers
    Route::get('/accounts/{accountId}/internal_transfers', [InternalTransferController::class, 'index']);
    Route::get('/accounts/{accountId}/internal_transfers/{id}', [InternalTransferController::class, 'show']);
    Route::post('/internal-transfers', [InternalTransferController::class, 'store']);
    Route::put('/internal-transfers/{id}', [InternalTransferController::class, 'update']);
    Route::delete('/internal-transfers/{id}', [InternalTransferController::class, 'destroy']);

    // Rutas para Investment Movements
    Route::get('/accounts/{accountId}/investment_movements', [InvestmentMovementController::class, 'index']);
    Route::get('/accounts/{accountId}/investment_movements/{id}', [InvestmentMovementController::class, 'show']);
    Route::post('/investment-movements', [InvestmentMovementController::class, 'store']);
    Route::put('/investment-movements/{id}', [InvestmentMovementController::class, 'update']);
    Route::delete('/investment-movements/{id}', [InvestmentMovementController::class, 'destroy']);

    // Rutas para Payment Services
    Route::get('/accounts/{accountId}/payment_services', [PaymentServiceController::class, 'index']);
    Route::get('/accounts/{accountId}/payment_services/{id}', [PaymentServiceController::class, 'show']);
    Route::post('/payment-services', [PaymentServiceController::class, 'store']);
    Route::put('/payment-services/{id}', [PaymentServiceController::class, 'update']);
    Route::delete('/payment-services/{id}', [PaymentServiceController::class, 'destroy']);

    // Rutas para Payment CC
    Route::get('/credit-cards/{cardId}/payment_ccs', [PaymentCcController::class, 'index']);
    Route::get('/credit-cards/{cardId}/payment_ccs/{id}', [PaymentCcController::class, 'show']);
    Route::post('/credit-cards/{cardId}/payment_ccs', [PaymentCcController::class, 'store']);
    Route::put('/credit-cards/{cardId}/payment_ccs/{id}', [PaymentCcController::class, 'update']);
    Route::delete('/payment_ccs/{id}', [PaymentCcController::class, 'destroy']);

    // Rutas para Separate Movements
    Route::get('/accounts/{accountId}/separate_movements', [SeparateMovementController::class, 'index']); 
    Route::get('/accounts/{accountId}/separate_movements/{id}', [SeparateMovementController::class, 'show']); 
    Route::post('/separate-movements', [SeparateMovementController::class, 'store']); 
    Route::put('/separate-movements/{id}', [SeparateMovementController::class, 'update']);
    Route::delete('/separate-movements/{id}', [SeparateMovementController::class, 'destroy']); 

    //Rutas para Usuarios
    Route::get('/users/{id}', [UserController::class, 'show']); 
    Route::post('/users', [UserController::class, 'store']); 
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']); 
});
