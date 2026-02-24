<?php

// namespace App\Console;

// use Illuminate\Console\Scheduling\Schedule;
// use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

// class Kernel extends ConsoleKernel
// {
//     /**
//      * Define the application's command schedule.
//      */
//     protected function schedule(Schedule $schedule): void
//     {
//         // $schedule->command('inspire')->hourly();
//     }

//     /**
//      * Register the commands for the application.
//      */
//     protected function commands(): void
//     {
//         $this->load(__DIR__.'/Commands');

//         require base_path('routes/console.php');
//     }
// }




namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\AutoLeadMessage;
use App\Console\Commands\TriggerWhatsApp; 
use App\Console\Commands\SendScheduledCampaigns; 

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        AutoLeadMessage::class,
        TriggerWhatsApp::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('automation:send-leads-to-whatsapp')->everyFiveMinutes();
        $schedule->command('campaign:send-scheduled')->everyMinute();
        $schedule->command('trigger:whatsapp')->everyMinute(); 
        // $schedule->command('whatsapp:trigger')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
