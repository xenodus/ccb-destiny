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
        $schedule->command('update:manifest')->weeklyOn(3, '01:05')->appendOutputTo($filePath);

        // Member Characters
        $schedule->command('update:characters')->hourly()->appendOutputTo($filePath);

        // Member Exotic Collection
        $schedule->command('update:memberExotic')->hourly()->appendOutputTo($filePath);

        // Member Platform Profile
        $schedule->command('update:memberPlatformProfile')->hourly()->appendOutputTo($filePath);

        // Seal Progression
        $schedule->command('update:seals')->hourly()->appendOutputTo($filePath);

        // Milestones
        // $schedule->command('update:milestones')->hourlyAt(2)->appendOutputTo($filePath);
        $schedule->command('update:milestones')->dailyAt('01:02')->appendOutputTo($filePath);

        // Vendor Stuff
        // $schedule->command('update:vendors')->hourlyAt(2)->appendOutputTo($filePath);
        $schedule->command('update:vendors')->dailyAt('01:02')->appendOutputTo($filePath);

        // Raid lockouts
        $schedule->command('update:lockouts')->everyFifteenMinutes()->appendOutputTo($filePath);

        // Stats
        $schedule->command('update:raidStats')->everyFifteenMinutes()->appendOutputTo($filePath);
        $schedule->command('update:PVEStats')->everyFifteenMinutes()->appendOutputTo($filePath);
        $schedule->command('update:PVPStats')->everyFifteenMinutes()->appendOutputTo($filePath);
        $schedule->command('update:GambitStats')->everyFifteenMinutes()->appendOutputTo($filePath);
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
