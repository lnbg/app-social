<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\User;
use App\Models\FacebookAnalytics;
use App\Models\FacebookPost;
use App\Models\FacebookFan;
use DateTime;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk as LaravelFacebookSDK;
use Goutte\Client;
use GuzzleHttp\Client as Client2;
use App\Helpers\FacebookHelper;

class FetchFacebookData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $facebook;
    protected $accessToken;
    protected $facebookHelper;
    protected $laravelFacebookSDK;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($facebook, $accessToken)
    {
        $this->facebook = $facebook;
        $this->accessToken = $accessToken;
        $this->facebookHelper = new FacebookHelper();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(LaravelFacebookSDK $laravelFacebookSDK)
    {
        $this->laravelFacebookSDK = $laravelFacebookSDK;
        try {
            $now = date('Y-m-d');
            $effectiveDate = date('Y-m-d', strtotime($now . "-1 years"));
            FacebookPost::where('facebook_analytics_id', '=', $this->facebook->id)
                            ->where('facebook_created_at', '<', $effectiveDate)->delete();   
            \Log::info("Update all old post of page: " . $this->facebook->account_name);
            $this->facebookHelper->facebookFanpageUpdateEachPostByAnalyticsID($this->laravelFacebookSDK, $this->accessToken, $this->facebook->id);
            \Log::info("Fetching new posts of page: " . $this->facebook->account_name);
            $result = $this->analyticsNewPostFacebookPage($this->laravelFacebookSDK, $this->accessToken, $this->facebook->id);
            \Log::info("page: " . $this->facebook->account_name . " done!");
        } catch (Facebook\Exceptions\FacebookSDKException $ex) {
            \Log::error("page: " . $this->facebook->account_name . " error!");
            \Log::errore($ex->getMessage());
        }
    }

    protected function analyticsNewPostFacebookPage(LaravelFacebookSDK $laravelFacebookSDK, $accessToken, $pageID)
    {
        try {
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
            // // get followers from graph api
            // $client = new Client();
            // $crawler = $client->request('GET', 'https://www.facebook.com/' . $facebookFanpageUserName);
            // $nodeFollowers = $crawler->filter('div._4bl9')->eq(2)->extract(array('_text', 'class', 'href'));
            // $analyticsData['total_page_followers'] = intval(preg_replace( '/[^0-9]/', '', $nodeFollowers[0][0]));
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
            $facebookAnalytics->total_posts = $analyticsPostObject->total;
            $facebookAnalytics->total_posts_shares = $analyticsPostObject->shares;
            $facebookAnalytics->total_posts_comments = $analyticsPostObject->comments;
            $facebookAnalytics->total_days = 365;
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
            $facebookAnalytics->average_posts_per_day = round($facebookAnalytics->total_posts / $facebookAnalytics->total_days, 2);
            $facebookAnalytics->average_reactions_per_post = round($reactions / $facebookAnalytics->total_days, 2);
            $facebookAnalytics->average_interactions_per_post = round($interactions / $facebookAnalytics->total_days, 2);
            
            $facebookAnalytics->save(); 
            return true;
        }
        catch (Facebook\Exceptions\FacebookSDKException $ex) {
            \Log::errore($ex->getMessage());
        }
    }
}
