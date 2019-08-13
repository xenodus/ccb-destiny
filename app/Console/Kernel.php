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
        $baseFilePath = '/var/www/sites/ccb/storage/logs/';

        // D2 Manifest
        $schedule->command('update:manifest')->weeklyOn(3, '01:05')->appendOutputTo($baseFilePath . 'manifest.log');

        // Member Characters
        $schedule->command('update:characters')->hourly()->appendOutputTo($baseFilePath . 'characters.log');

        // Member Exotic Collection
        $schedule->command('update:memberExotic')->hourly()->appendOutputTo($baseFilePath . 'exotics.log');

        // Member Platform Profile
        $schedule->command('update:memberPlatformProfile')->hourly()->appendOutputTo($baseFilePath . 'platform.log');

        // Seal Progression
        $schedule->command('update:seals')->hourly()->appendOutputTo($baseFilePath . 'seals.log');

        // Milestones
        $schedule->command('update:milestones')->hourlyAt(2)->appendOutputTo($baseFilePath . 'milestones.log');
        //$schedule->command('update:milestones')->dailyAt('01:02')->appendOutputTo($filePath);
        $schedule->command('update:milestones')->weeklyOn(3, '01:05')->appendOutputTo($baseFilePath . 'milestones.log');

        // Vendor Stuff
        $schedule->command('update:vendors')->hourlyAt(2)->appendOutputTo($baseFilePath . 'vendors.log');
        //$schedule->command('update:vendors')->dailyAt('01:02')->appendOutputTo($filePath);
        $schedule->command('update:vendors')->weeklyOn(3, '01:05')->appendOutputTo($baseFilePath . 'vendors.log');

        // Clan Activity Buddy
        $schedule->command('update:clanActivityBuddies')->weeklyOn(3, '05:33')->appendOutputTo($baseFilePath . 'buddy.log');

        // Raid lockouts
        $schedule->command('update:lockouts')->everyFifteenMinutes()->appendOutputTo($baseFilePath . 'lockouts.log');

        // Stats
        $schedule->command('update:raidStats')->everyFifteenMinutes()->appendOutputTo($baseFilePath . 'stats.log');
        $schedule->command('update:PVEStats')->everyFifteenMinutes()->appendOutputTo($baseFilePath . 'stats.log');
        $schedule->command('update:PVPStats')->everyFifteenMinutes()->appendOutputTo($baseFilePath . 'stats.log');
        $schedule->command('update:GambitStats')->everyFifteenMinutes()->appendOutputTo($baseFilePath . 'stats.log');
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
