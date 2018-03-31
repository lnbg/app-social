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
        Commands\KolsFetchingFacebookPage::class,
        Commands\KolsFetchingInstagramProfile::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $file = 'storage/logs/kols_fetching_instagram.txt';
        
        $schedule->command('kols:instagram')
                ->timezone('Asia/Saigon')
                ->dailyAt('01:00')
                ->sendOutputTo($file);

        $file = 'storage/logs/kols_fetching_facebook_page.txt';
                
        $schedule->command('kols:facebook_page')
                ->timezone('Asia/Saigon')
                ->dailyAt('02:00')
                ->sendOutputTo($file);
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
