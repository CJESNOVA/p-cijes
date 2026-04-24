<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RessourceCompteCallbackController;
use App\Http\Controllers\Api\RewardApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\ModuleressourceController;

Route::post('/callback/ressourcecompte/{transaction}', 
    [RessourceCompteCallbackController::class, 'handle']
)->name('api.callback.ressourcecompte');

// Routes API pour la gestion des récompenses
Route::prefix('rewards')->group(function () {
    Route::post('/attribute', [RewardApiController::class, 'attribuerRecompense']);
    Route::get('/actions', [RewardApiController::class, 'listerActions']);
    Route::get('/member/rewards', [RewardApiController::class, 'verifierRecompenses']);
});

// Routes API pour la gestion des paiements
Route::prefix('payments')->group(function () {
    Route::post('/trigger', [PaymentApiController::class, 'triggerPayment']);
    Route::get('/actions', [PaymentApiController::class, 'listPaymentActions']);
    Route::get('/status/{reference}', [PaymentApiController::class, 'checkPaymentStatus']);
});

// Routes API pour la gestion des modules ressources
Route::prefix('modules')->group(function () {
    Route::get('/', [ModuleressourceController::class, 'index']);
    Route::get('/{id}', [ModuleressourceController::class, 'show']);
    Route::post('/attribute', [ModuleressourceController::class, 'attribuerModuleRessource']);
});

