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

        // Telegram Raid Event Notification
        $schedule->command('send:TelegramRaidNotifications')->everyMinute();

        // D2 Manifest
        $schedule->command('update:manifest')->weeklyOn(env('DESTINY_RESET_DAY'), env('DESTINY_RESET_HOUR').':03'); //->appendOutputTo($baseFilePath . 'manifest.log'); // After Reset

        // Member Aliases
        $schedule->command('update:memberAlias')->hourly(); //->appendOutputTo($baseFilePath . 'alias.log');

        // Member Characters
        $schedule->command('update:characters')->hourly(); //->appendOutputTo($baseFilePath . 'characters.log');

        // Member Exotic Collection
        // $schedule->command('update:memberExotic')->hourlyAt(30)->appendOutputTo($baseFilePath . 'exotics.log');

        // Member Platform Profile
        $schedule->command('update:memberPlatformProfile')->hourlyAt(15); //->appendOutputTo($baseFilePath . 'platform.log');

        // Seal Progression
        $schedule->command('update:seals')->hourly(); //->appendOutputTo($baseFilePath . 'seals.log');

        // Milestones
        $schedule->command('update:milestones')->hourlyAt(2); //->appendOutputTo($baseFilePath . 'milestones.log');
        $schedule->command('update:milestones')->weeklyOn(env('DESTINY_RESET_DAY'), env('DESTINY_RESET_HOUR').':05'); //->appendOutputTo($baseFilePath . 'milestones.log'); // After Reset

        $schedule->command('update:nightfalls')->hourlyAt(2); //->appendOutputTo($baseFilePath . 'nightfalls.log');
        $schedule->command('update:nightfalls')->weeklyOn(env('DESTINY_RESET_DAY'), env('DESTINY_RESET_HOUR').':05'); //->appendOutputTo($baseFilePath . 'nightfalls.log'); // After Reset

        // Vendor Stuff
        $schedule->command('update:vendors')->hourlyAt(2); //->appendOutputTo($baseFilePath . 'vendors.log');
        $schedule->command('update:vendors')->weeklyOn(env('DESTINY_RESET_DAY'), env('DESTINY_RESET_HOUR').':05'); //->appendOutputTo($baseFilePath . 'vendors.log'); // After Reset

        // Telegram Xur Notification - Ensure after Vendor
        $schedule->command('send:TelegramXurNotifications')->weeklyOn(6, env('XUR_TELEGRAM_NOTIFY_TIME')); //->appendOutputTo($baseFilePath . 'telegram.log'); // After Reset

        // Raid lockouts
        // $schedule->command('update:lockouts')->everyFifteenMinutes()->appendOutputTo($baseFilePath . 'lockouts.log');

        // Stats
        $schedule->command('update:raidStats')->hourly(); //->appendOutputTo($baseFilePath . 'stats.log');
        $schedule->command('update:PVEStats')->hourly(); //->appendOutputTo($baseFilePath . 'stats.log');
        $schedule->command('update:PVPStats')->hourly(); //->appendOutputTo($baseFilePath . 'stats.log');
        $schedule->command('update:GambitStats')->hourly(); //->appendOutputTo($baseFilePath . 'stats.log');
        $schedule->command('update:GambitPrimeStats')->hourly(); //->appendOutputTo($baseFilePath . 'stats.log');
        // $schedule->command('update:TrialsStats')->everyFifteenMinutes()->appendOutputTo($baseFilePath . 'stats.log');
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
