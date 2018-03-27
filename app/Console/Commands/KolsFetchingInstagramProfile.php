<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Smochin\Instagram\Crawler;
use App\Helpers\InstagramHelper;

use App\Models\InstagramAnalytics;
use App\Models\InstagramMedia;

class KolsFetchingInstagramProfile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kols:instagram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching instagram profile';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $instagramCrawler;

    protected $instagramHelper;
    
    public function __construct(Crawler $instagramCrawler, InstagramHelper $instagramHelper) 
    {
        parent::__construct();
        $this->instagramCrawler = $instagramCrawler;
        $this->instagramHelper = $instagramHelper;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $allInstagramProfiles = InstagramAnalytics::all();
        $this->line("fetching instgram profile data: " . date('Y-m-d'));
        foreach ($allInstagramProfiles as $instagramProfile) {
            $this->line("Fetching data of instagram profile: " . $instagramProfile->name);
            $this->instagramHelper->analyticsInstagramProfile($instagramProfile->id);
            $this->line("instagram profile: " . $instagramProfile->name . "  done!");
        }
    }
}
