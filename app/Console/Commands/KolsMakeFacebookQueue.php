<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FacebookAnalytics;
use App\Models\User;

use App\Jobs\FetchFacebookData;

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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = User::where('type', '=', 2)->first();
        $accessToken = $user->access_token;
        $facebookAnalytics = FacebookAnalytics::all();
        \DB::table('jobs')->truncate();
        foreach ($facebookAnalytics as $page) {
            FetchFacebookData::dispatch($page, $accessToken);
        }
    }
}
