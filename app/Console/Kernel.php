<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $filePath = '/var/www/sites/ccb/storage/logs/artisan.log';

        // D2 Manifest
        $schedule->command('update:manifest')->dailyAt('01:10')->appendOutputTo($filePath);

        // Raid lockouts
        $schedule->command('update:lockouts')->everyThirtyMinutes()->appendOutputTo($filePath);
        // Member Characters
        $schedule->command('update:characters')->everyThirtyMinutes()->appendOutputTo($filePath);
        // Member Exotic Collection
        $schedule->command('update:memberExotic')->everyThirtyMinutes()->appendOutputTo($filePath);
        // Member Platform Profile
        $schedule->command('update:memberPlatformProfile')->everyThirtyMinutes()->appendOutputTo($filePath);
        // Seal Progression
        $schedule->command('update:seals')->everyFiveMinutes()->appendOutputTo($filePath);
        // Weekly Nightfalls
        $schedule->command('update:nightfalls')->hourlyAt(2)->appendOutputTo($filePath);
        // Vendor Stuff
        $schedule->command('update:vendors')->hourlyAt(2)->appendOutputTo($filePath);

        // Stats
        $schedule->command('update:raidStats')->everyFiveMinutes()->appendOutputTo($filePath);
        $schedule->command('update:PVEStats')->everyFiveMinutes()->appendOutputTo($filePath);
        $schedule->command('update:PVPStats')->everyFiveMinutes()->appendOutputTo($filePath);
        $schedule->command('update:GambitStats')->everyFiveMinutes()->appendOutputTo($filePath);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
