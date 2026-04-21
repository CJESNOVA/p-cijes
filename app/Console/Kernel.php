<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\RenouvelerAbonnements::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Exécuter le renouvellement automatique tous les jours à minuit
        $schedule->command('abonnement:renouveler')
                ->daily()
                ->at('00:00')
                ->withoutOverlapping()
                ->runInBackground();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
