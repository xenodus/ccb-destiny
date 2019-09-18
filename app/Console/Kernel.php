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
        $schedule->command('update:manifest')->timezone('America/Los_Angeles')->weeklyOn(2, '10:05')->appendOutputTo($baseFilePath . 'manifest.log'); // After Reset

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
        $schedule->command('update:milestones')->timezone('America/Los_Angeles')->weeklyOn(2, '10:05')->appendOutputTo($baseFilePath . 'milestones.log'); // After Reset

        $schedule->command('update:nightfalls')->hourlyAt(2)->appendOutputTo($baseFilePath . 'nightfalls.log');
        $schedule->command('update:nightfalls')->timezone('America/Los_Angeles')->weeklyOn(2, '10:05')->appendOutputTo($baseFilePath . 'nightfalls.log'); // After Reset

        // Vendor Stuff
        $schedule->command('update:vendors')->hourlyAt(2)->appendOutputTo($baseFilePath . 'vendors.log');
        $schedule->command('update:vendors')->timezone('America/Los_Angeles')->weeklyOn(2, '10:05')->appendOutputTo($baseFilePath . 'vendors.log'); // After Reset

        // Clan Activity Buddy
        $schedule->command('update:clanRaidActivityBuddies')->dailyAt('03:00')->runInBackground()->appendOutputTo($baseFilePath . 'raid_buddy.log');
        $schedule->command('update:clanPvPActivityBuddies')->dailyAt('03:00')->runInBackground()->appendOutputTo($baseFilePath . 'pvp_buddy.log');
        $schedule->command('update:clanGambitActivityBuddies')->dailyAt('03:00')->runInBackground()->appendOutputTo($baseFilePath . 'gambit_buddy.log');
        $schedule->command('update:clanGambitPrimeActivityBuddies')->dailyAt('03:00')->runInBackground()->appendOutputTo($baseFilePath . 'gambit_prime_buddy.log');

        // Raid lockouts
        $schedule->command('update:lockouts')->everyFifteenMinutes()->appendOutputTo($baseFilePath . 'lockouts.log');

        // Stats
        $schedule->command('update:raidStats')->everyFifteenMinutes()->appendOutputTo($baseFilePath . 'stats.log');
        $schedule->command('update:PVEStats')->everyFifteenMinutes()->appendOutputTo($baseFilePath . 'stats.log');
        $schedule->command('update:PVPStats')->everyFifteenMinutes()->appendOutputTo($baseFilePath . 'stats.log');
        $schedule->command('update:GambitStats')->everyFifteenMinutes()->appendOutputTo($baseFilePath . 'stats.log');
        $schedule->command('update:GambitPrimeStats')->everyFifteenMinutes()->appendOutputTo($baseFilePath . 'stats.log');
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
