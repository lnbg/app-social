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
        $facebookAnalytics = FacebookAnalytics::where('account_type', '=', 1)->get();
        $this->line("fetching facebook page data: " . date('Y-m-d'));
        $user = User::first();
        $accessToken = $user->access_token;
        foreach ($facebookAnalytics as $page) {
            if ($page->id > 25) {
                $now = date('Y-m-d');
                $effectiveDate = date('Y-m-d', strtotime($now . "-1 years"));
                FacebookPost::where('facebook_analytics_id', '=', $page->id)
                                ->where('facebook_created_at', '<', $effectiveDate)->delete();   
                $this->line("Update all old post of page: " . $page->account_name);
                $this->facebookHelper->facebookFanpageUpdateEachPostByAnalyticsID($this->laravelFacebookSDK, $user->access_token, $page->id);
                $this->line("Fetching new posts of page: " . $page->account_name);
                try {
                    $result = $this->analyticsNewPostFacebookPage($this->laravelFacebookSDK, $accessToken, $page->id);
                    $this->line("page: " . $page->account_name . " done!");
                } catch (\Exception $ex) {
                    $this->line("page: " . $page->account_name . " error!");
                    $this->line($ex);
                }
            }
        }
    }

    protected function analyticsNewPostFacebookPage(LaravelFacebookSDK $laravelFacebookSDK, $accessToken, $pageID)
    {
        // get request data
        $user = User::first(); // get user to get access token
        $id = $pageID;
        // Meta data
        $facebookAnalytics = FacebookAnalytics::find($id);
        
        $now = date('Y-m-d');
        // get data facebook from two day ago.
        $effectiveDate = date('Y-m-d', strtotime($now . "-1 days"));
        //retrive yesterday's date in the format 9999-99-99
        $facebookFanpageLink = explode('/', $facebookAnalytics->account_link);
        $facebookFanpageUserName = $facebookFanpageLink[3];
        // storage all posts when analytics progressing
        $postsStorage = [];
        // init analytics data for facebook_analytics table
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
        // get followers from graph api
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.facebook.com/' . $facebookFanpageUserName);
        $nodeFollowers = $crawler->filter('div._4bl9')->eq(2)->extract(array('_text', 'class', 'href'));
        $analyticsData['total_page_followers'] = intval(preg_replace( '/[^0-9]/', '', $nodeFollowers[0][0]));
        // get diff days when analytics
        $dteNow = new DateTime($now);
        $dteEffective = new DateTime($effectiveDate);
        $diffDays = $dteNow->diff($dteEffective)->days;
        // analytics facebook_page data
        $this->facebookHelper->facebookFanpageGetPostByURI($laravelFacebookSDK, $facebookAnalytics->id,
            $user->access_token, $facebookFanpageUserName, $postsStorage, $effectiveDate);
        // get page likes
        $analyticsData['total_page_likes'] = $this->facebookHelper->getPageLikes($laravelFacebookSDK, $user->access_token, $facebookFanpageUserName);
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
        // get again pictures avatar of facebook page
        $defaultPictureGraphAPIURI = '/' . $facebookFanpageUserName;
        $picture = $laravelFacebookSDK->sendRequest('GET', $defaultPictureGraphAPIURI, ['fields' => 'cover,picture.width(800).height(800)'], $user->access_token);
        $picture = $picture->getDecodedBody();
        if (isset($picture['picture']['data']['url'])) {
            $facebookAnalytics->account_picture = $picture['picture']['data']['url'];
        }
        if (isset($picture['cover']['source'])) {
            $facebookAnalytics->account_picture_cover = $picture['cover']['source'];
        }

        FacebookPost::insert($postsStorage);

        $analyticsPostObject = FacebookPost::where('facebook_analytics_id', '=', $facebookAnalytics->id)
            ->select(\DB::raw('count(facebook_post_id) as total, sum(reaction_like) as likes, sum(reaction_haha) as hahas, sum(reaction_wow) as wows,
            sum(reaction_love) as loves, sum(reaction_sad) as sads, sum(reaction_angry) as angries, sum(reaction_thankful) as thankfuls, sum(comments) as comments, sum(shares) as shares'))->first();
        
        $facebookAnalytics->total_page_likes = $analyticsData['total_page_likes'];
        $facebookAnalytics->total_page_followers = $analyticsData['total_page_followers'];
        $facebookAnalytics->total_posts = $analyticsPostObject->total;
        $facebookAnalytics->total_posts_shares = $analyticsPostObject->shares;
        $facebookAnalytics->total_posts_comments = $analyticsPostObject->comments;
        $facebookAnalytics->total_posts_likes = $analyticsPostObject->likes;
        $facebookAnalytics->total_posts_hahas = $analyticsPostObject->hahas;
        $facebookAnalytics->total_posts_wows = $analyticsPostObject->wows;
        $facebookAnalytics->total_posts_loves = $analyticsPostObject->loves;
        $facebookAnalytics->total_posts_thankfuls = $analyticsPostObject->thankfuls;
        $facebookAnalytics->total_posts_angries = $analyticsPostObject->angries;
        $reactions = $facebookAnalytics->total_posts_likes + $facebookAnalytics->total_posts_hahas + 
                    $facebookAnalytics->total_posts_wows + $facebookAnalytics->total_posts_loves + 
                    $facebookAnalytics->total_posts_sads + $facebookAnalytics->total_posts_angries +
                    $facebookAnalytics->total_posts_thankfuls;
                    
        $interactions = $reactions + $facebookAnalytics->total_posts_shares + $facebookAnalytics->total_posts_comments;
        $facebookAnalytics->average_reactions_per_post = round($reactions / $facebookAnalytics->total_days, 2);
        $facebookAnalytics->average_interactions_per_post = round($interactions / $facebookAnalytics->total_days, 2);
        
        $facebookAnalytics->save(); 
        return true;
    }
}
