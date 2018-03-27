<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FacebookAnalytics;
use App\Models\FacebookPost;
use App\Models\FacebookFan;
use DateTime;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk as LaravelFacebookSDK;
use Goutte\Client;
use GuzzleHttp\Client as Client2;
use App\Helpers\FacebookHelper;

class FacebookAnalyticsController extends Controller
{

    /**
     * Facebook Helpers
     *
     * @var FacebookHelper
     */
    protected $facebookHelper;
    
    public function __construct(FacebookHelper $facebookHelper) 
    {
        $this->facebookHelper = $facebookHelper;
    }

    /**
     * get all records of table facebook_analytics
     */
    public function getListFacebookPageAnalytics()
    {
        $listFacebookPageAnalytics = FacebookAnalytics::all();
        return response()->json($listFacebookPageAnalytics, 200);
    }
    
    public function createNewFacebookPage(LaravelFacebookSDK $laravelFacebookSDK, Request $request)
    {
        try {
            $accessToken = $user = User::first()->access_token;
            $facebookFanpageLink = $request->page_link;
            $facebookFanpageLink = explode('/', $facebookFanpageLink);
            $facebookFanpageName = $facebookFanpageLink[3];
            $defaultURI = '/' . $facebookFanpageName;
            $posts = $laravelFacebookSDK->sendRequest('GET', $defaultURI, ['fields' => 'name,picture'], $accessToken);
            $posts = $posts->getDecodedBody();
            $new = FacebookAnalytics::create([
                'account_id' => $posts['id'],
                'account_name' => $posts['name'],
                'account_link' => $request->page_link,
                'account_picture' => $posts['picture']['data']['url']
            ]);
            return response()->json($new, 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
        
    }

    public function analyticsFacebookPage(LaravelFacebookSDK $laravelFacebookSDK, Request $request)
    {
        // try {
            // get request data
            $user = User::first(); // get user to get access token
            $id = $request->id;
            // Meta data
            $facebookAnalytics = FacebookAnalytics::find($id);
            $now = date('Y-m-d');
            $effectiveDate = date('Y-m-d', strtotime($now . "-3 months"));
            //retrive yesterday's date in the format 9999-99-99
            $facebookFanpageLink = explode('/', $facebookAnalytics->account_link);
            $facebookPageName = $facebookFanpageLink[3];
            $postsStorage = [];
            $accountActivedDate = date('Y-m-d');
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
            // get followers
            $client = new Client();
            $crawler = $client->request('GET', 'https://www.facebook.com/' . $facebookPageName);
            $nodeFollowers = $crawler->filter('div._4bl9')->eq(2)->extract(array('_text', 'class', 'href'));
            $analyticsData['total_page_followers'] = intval(preg_replace( '/[^0-9]/', '', $nodeFollowers[0][0]));

            $now = date('Y-m-d');
            $dteNow = new DateTime($now);
            $dteEffective = new DateTime($effectiveDate);
            $diffDays = $dteNow->diff($dteEffective)->days;

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
            
            FacebookPost::where('facebook_analytics_id', '=', $facebookAnalytics->id)->delete();
            FacebookPost::insert($postsStorage);
            
            return response()->json($facebookAnalytics, 200);
            
        // } catch (\Exception $e) {
        //     return response()->json($ex, 500);
        // }
    }
    
    public function debug(LaravelFacebookSDK $laravelFacebookSDK, Request $request)
    {
        $user = User::first();
        $defaultURI = '/1124060737634509/insights';
        $posts = $laravelFacebookSDK->sendRequest('GET', $defaultURI, 
        ['limit' => 100, 'since' => '2018-02-12', 'until' => '2018-03-12'], $user->access_token);
        dd($posts);
    }
}
