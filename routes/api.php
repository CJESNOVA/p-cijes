<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RessourceCompteCallbackController;

Route::post('/callback/ressourcecompte/{transaction}', 
    [RessourceCompteCallbackController::class, 'handle']
)->name('api.callback.ressourcecompte');

