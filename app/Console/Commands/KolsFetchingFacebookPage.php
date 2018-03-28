<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User;
use App\Models\FacebookAnalytics;
use App\Models\FacebookPost;
use App\Models\FacebookFan;
use DateTime;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk as LaravelFacebookSDK;
use Goutte\Client;
use GuzzleHttp\Client as Client2;
use App\Helpers\FacebookHelper;

class KolsFetchingFacebookPage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kols:facebook_page';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching Facebook Page';

    /**
     * LaravelFacebookSDK
     *
     * @var LaravelFacebookSDK
     */
    protected $laravelFacebookSDK;
    
    /**
     * FacebookHelper
     *
     * @var FacebookHelper
     */
    protected $facebookHelper;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(LaravelFacebookSDK $laravelFacebookSDK,FacebookHelper $facebookHelper)
    {
        parent::__construct();
        $this->laravelFacebookSDK = $laravelFacebookSDK;
        $this->facebookHelper = $facebookHelper;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $truncate = FacebookPost::truncate();
        if ($truncate) {
            $facebookAnalytics = FacebookAnalytics::where('account_type', '=', 1)->get();
            $this->line("fetching facebook page data: " . date('Y-m-d'));
            $this->line("truncate table facebook posts");
            foreach ($facebookAnalytics as $page) {
                $this->line("Fetching data of page: " . $page->account_name);
                try {
                    $result = $this->analyticsFacebookPage($this->laravelFacebookSDK, $page->id);
                    $this->line("page: " . $page->account_name . " done!");
                } catch (\Exception $ex) {
                    $this->line("page: " . $page->account_name . " error!");
                }
            }
        }
    }

    protected function analyticsFacebookPage(LaravelFacebookSDK $laravelFacebookSDK, $pageID)
    {
        // get request data
        $user = User::first(); // get user to get access token
        $id = $pageID;
        // Meta data
        $facebookAnalytics = FacebookAnalytics::find($id);
        $now = date('Y-m-d');
        // get data facebook from one day ago.
        $effectiveDate = date('Y-m-d', strtotime($now . '-3 months'));
        //retrive yesterday's date in the format 9999-99-99
        $facebookFanpageLink = explode('/', $facebookAnalytics->account_link);
        $facebookPageName = $facebookFanpageLink[3];
        // store all post from one day ago
        $postsStorage = [];
        // init analytics data need get
        $analyticsData = [
            'total_posts' => 0,
            'total_page_likes' => 0,
            'total_page_followers' => 0,
            'total_posts_likes' => 0,
            'total_posts_loves' => 0,
            'total_posts_hahas' => 0,
            'total_posts_wows' => 0,
            'total_posts_sads' => 0,
            'total_posts_angries' => 0,
            'total_posts_shares' => 0,
            'total_posts_comments' => 0,
            'total_posts_thankfuls' => 0
        ];            
        // get facebook page followers
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.facebook.com/' . $facebookPageName);
        $nodeFollowers = $crawler->filter('div._4bl9')->eq(2)->extract(array('_text', 'class', 'href'));
        $analyticsData['total_page_followers'] = intval(preg_replace( '/[^0-9]/', '', $nodeFollowers[0][0]));
        
        // get all posts of page from one day ago.
        $now = date('Y-m-d');
        $dteNow = new DateTime($now);
        $dteEffective = new DateTime($effectiveDate);
        // diffdays = 1 for commands
        $diffDays = $dteNow->diff($dteEffective)->days;
        // get all posts from one day ago
        $this->facebookHelper->facebookFanpageGetPostByURI($laravelFacebookSDK, $facebookAnalytics->id, 
            $user->access_token, $facebookPageName, $analyticsData, $postsStorage, $effectiveDate);
            
        // get page likes
        $analyticsData['total_page_likes'] = $this->facebookHelper->getPageLikes($laravelFacebookSDK, $user->access_token, $facebookPageName);
        
        $facebookFan = FacebookFan::where(
            [
                ['facebook_analytics_id', '=', $facebookAnalytics->id],
                ['date_sync', '=', $now]
            ])->first();
        if (empty($facebookFan)) {
            FacebookFan::create([
                'facebook_analytics_id' => $facebookAnalytics->id,
                'facebook_fans' => $analyticsData['total_page_likes'],
                'date_sync' => $now,
            ]);
        } else {
            $facebookFan->facebook_fans = $analyticsData['total_page_likes'];
            $facebookFan->save();
        }

        $facebookAnalytics->total_posts = $analyticsData['total_posts'];
        $facebookAnalytics->total_page_likes = $analyticsData['total_page_likes'];
        $facebookAnalytics->total_page_followers = $analyticsData['total_page_followers'];
        $facebookAnalytics->total_days = $diffDays;
        $facebookAnalytics->total_posts_likes = $analyticsData['total_posts_likes'];
        $facebookAnalytics->total_posts_shares = $analyticsData['total_posts_shares'];
        $facebookAnalytics->total_posts_comments = $analyticsData['total_posts_comments'];
        $facebookAnalytics->total_posts_hahas = $analyticsData['total_posts_hahas'];
        $facebookAnalytics->total_posts_wows = $analyticsData['total_posts_wows'];
        $facebookAnalytics->total_posts_loves = $analyticsData['total_posts_loves'];
        $facebookAnalytics->total_posts_sads = $analyticsData['total_posts_sads'];
        $facebookAnalytics->total_posts_thankfuls = $analyticsData['total_posts_thankfuls'];
        $facebookAnalytics->total_posts_angries = $analyticsData['total_posts_angries'];
        $facebookAnalytics->average_posts_per_day = round($facebookAnalytics->total_posts / $facebookAnalytics->total_days, 2);
        $reactions = $facebookAnalytics->total_posts_likes + $facebookAnalytics->total_posts_hahas + 
                        $facebookAnalytics->total_posts_wows + $facebookAnalytics->total_posts_loves + 
                        $facebookAnalytics->total_posts_sads + $facebookAnalytics->total_posts_angries;
                        
        $interactions = $reactions + $facebookAnalytics->total_posts_shares + $facebookAnalytics->total_posts_comments + $facebookAnalytics->total_posts_thankfuls;
        $facebookAnalytics->average_reactions_per_post = round($reactions / $facebookAnalytics->total_days, 2);
        $facebookAnalytics->average_interactions_per_post = round($interactions / $facebookAnalytics->total_days, 2);
        $facebookAnalytics->save();

        FacebookPost::insert($postsStorage);
        return true;
        
    }
}
