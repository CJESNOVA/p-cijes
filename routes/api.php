<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RessourceCompteCallbackController;
use App\Http\Controllers\Api\RewardApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DiagnosticController;
use App\Http\Controllers\Api\DiagnosticStructureController;
use App\Http\Controllers\ModuleRessourceController;

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
    Route::get('/', [ModuleRessourceController::class, 'index']);
    Route::get('/{id}', [ModuleRessourceController::class, 'show']);
    Route::post('/attribute', [ModuleRessourceController::class, 'attribuerModuleRessource']);
});

Route::prefix('v1')->group(function () {
    
    // Routes d'authentification (publiques)
    Route::prefix('auth')->group(function () {
        Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
        Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('api.token');
        Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me'])->middleware('api.token');
    });
    
    // Routes pour les diagnostics (protégées par token)
    Route::prefix('diagnostic')->middleware('api.token')->group(function () {
        // Récupérer tous les diagnostics complets pour un utilisateur via son email
        Route::get('/complet/{email}', [DiagnosticController::class, 'getDiagnosticComplet'])
            ->name('api.diagnostic.complet');
    });
    
    // Routes pour la structure des diagnostics (publiques)
    Route::prefix('diagnostic-structure')->group(function () {
        // Structure complète : modules -> questions -> réponses
        Route::get('/complete', [DiagnosticStructureController::class, 'getStructureComplete'])
            ->name('api.diagnostic.structure.complete');
            
        // Structure par module spécifique
        Route::get('/module/{moduleId}', [DiagnosticStructureController::class, 'getStructureByModule'])
            ->name('api.diagnostic.structure.module');
            
        // Liste des modules uniquement
        Route::get('/modules', [DiagnosticStructureController::class, 'getModulesList'])
            ->name('api.diagnostic.structure.modules');
            
        // Questions d'un module spécifique
        Route::get('/module/{moduleId}/questions', [DiagnosticStructureController::class, 'getQuestionsByModule'])
            ->name('api.diagnostic.structure.questions');
            
        // Structure par profil d'entreprise
        Route::get('/profil/{profilId}', [DiagnosticStructureController::class, 'getStructureByProfil'])
            ->name('api.diagnostic.structure.profil');
            
        // Types de modules disponibles
        Route::get('/types', [DiagnosticStructureController::class, 'getModuleTypes'])
            ->name('api.diagnostic.structure.types');
            
        // Profils d'entreprise disponibles
        Route::get('/profils', [DiagnosticStructureController::class, 'getEntrepriseProfils'])
            ->name('api.diagnostic.structure.profils');
    });
});

