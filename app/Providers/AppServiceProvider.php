<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Auth;
use App\Auth\SupabaseUserProvider;

use App\Models\Ressourcecompte;
use App\Models\Ressourcetransaction;
use App\Observers\RessourceCompteObserver;
use App\Observers\RessourceTransactionObserver;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        //Ressourcecompte::observe(RessourceCompteObserver::class);
        //Ressourcetransaction::observe(RessourceTransactionObserver::class);

        //
        Auth::provider('supabase', function($app, array $config) {
            return new SupabaseUserProvider();
        });
    }
}
