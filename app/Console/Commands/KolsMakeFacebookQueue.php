<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FacebookAnalytics;
use App\Models\User;

use App\Jobs\FetchFacebookData;
use App\Helpers\FacebookHelper;

class KolsMakeFacebookQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kols:facebook_queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make queue for facebook cronjobs';

    protected $facebookHelper;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(FacebookHelper $facebookHelper)
    {
        parent::__construct();
        $this->facebookHelper = $facebookHelper;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $accessToken = $this->facebookHelper->getFacebookAccessToken();
        $facebookAnalytics = FacebookAnalytics::all();
        \DB::table('jobs')->truncate();
        foreach ($facebookAnalytics as $page) {
            FetchFacebookData::dispatch($page, $accessToken);
        }
    }
}
