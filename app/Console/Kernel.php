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
        Commands\KolsFetchingInstagramProfile::class,
        Commands\KolsMakeFacebookQueue::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $file = 'storage/logs/kols_fetching_instagram.txt';

        $schedule->command('kols:instagram')
                ->timezone('Asia/Saigon')
                ->dailyAt('05:00')
                ->sendOutputTo($file);

        
        $schedule->command('kols:facebook_queue')
                ->timezone('Asia/Saigon')
                ->dailyAt('0:00');

        $schedule->command('queue:work --once --sleep=5 --timeout=3600')
                ->timezone('Asia/Saigon')
                ->dailyAt('00:02');
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
