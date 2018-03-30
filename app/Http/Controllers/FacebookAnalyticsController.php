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
            $facebookFanpageUserName = $facebookFanpageLink[3];
            $defaultURI = '/' . $facebookFanpageUserName;
            $posts = $laravelFacebookSDK->sendRequest('GET', $defaultURI, ['fields' => 'name,cover, picture.width(800).height(800)'], $accessToken);
            $posts = $posts->getDecodedBody();
            $new = FacebookAnalytics::create([
                'account_id' => $posts['id'],
                'account_name' => $posts['name'],
                'account_username' => $facebookFanpageUserName,
                'account_link' => $request->page_link,
                'account_picture' => $posts['picture']['data']['url'],
                'account_picture_cover' => $posts['cover']['source']
            ]);
            return response()->json($all, 200);
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
            $facebookPageUserName = $facebookFanpageLink[3];
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
            $crawler = $client->request('GET', 'https://www.facebook.com/' . $facebookPageUserName);
            $nodeFollowers = $crawler->filter('div._4bl9')->eq(2)->extract(array('_text', 'class', 'href'));
            $analyticsData['total_page_followers'] = intval(preg_replace( '/[^0-9]/', '', $nodeFollowers[0][0]));

            $now = date('Y-m-d');
            $dteNow = new DateTime($now);
            $dteEffective = new DateTime($effectiveDate);
            $diffDays = $dteNow->diff($dteEffective)->days;

            $this->facebookHelper->facebookFanpageGetPostByURI($laravelFacebookSDK, $facebookAnalytics->id,
                $user->access_token, $facebookPageUserName, $analyticsData, $postsStorage, $effectiveDate);

            // get page likes
            $analyticsData['total_page_likes'] = $this->facebookHelper->getPageLikes($laravelFacebookSDK, $user->access_token, $facebookPageUserName);

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

            $defaultPictureGraphAPIURI = '/' . $facebookFanpageUserName;
            $picture = $laravelFacebookSDK->sendRequest('GET', $defaultPictureGraphAPIURI, ['fields' => 'cover,picture.width(800).height(800)'], $user->access_token);
            $picture = $picture->getDecodedBody();
            if (isset($picture['picture']['data']['url'])) {
                $facebookAnalytics->account_picture = $picture['picture']['data']['url'];
            }
            if (isset($picture['cover']['source'])) {
                $facebookAnalytics->account_picture_cover = $picture['cover']['source'];
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
        //     return response()->json($e, 500);
        // }
    }

    public function getFacebookPageByFacebookAnalyticID(Request $request)
    {
        $now = date('Y-m-d');

        $pageOverview = FacebookAnalytics::where('account_username', '=', $request->username)->first();
        $growthFans = FacebookFan::where('facebook_analytics_id', '=', $pageOverview->id)->select('facebook_fans', 'date_sync')->get();

        $bestPost = FacebookPost::where(
            'facebook_analytics_id','=', $pageOverview->id)
        ->where(\DB::raw("reaction_like + reaction_wow + reaction_angry + reaction_haha + reaction_wow + reaction_sad + comments + shares"), '=',
        \DB::raw("(SELECT MAX(reaction_like + reaction_wow + reaction_angry + reaction_haha + reaction_wow + reaction_sad + comments + shares) FROM facebook_posts where facebook_analytics_id = " . $pageOverview->id . ")"))
        ->first();

        $lastPosts = FacebookPost::where(
            'facebook_analytics_id','=', $pageOverview->id)
        ->orderBy('facebook_created_at', 'desc')->limit(5)->get();

        $lastPostsResults = [];
        $lastPostsResultsKey = 0;
        $lastPostsResultDay = '';
        
        foreach ($lastPosts as $post) {
            $currentDay = date('Y-m-d', strtotime($post['facebook_created_at']));
            if ($lastPostsResultDay == '') {
                $lastPostsResultDay = $currentDay;
                $lastPostsResults[$lastPostsResultsKey] = [
                    'date' => $lastPostsResultDay,
                    'posts' => [$post]
                ];
            } else {
                // truong hop 1 khi last post = current post
                if ($lastPostsResultDay != $currentDay) {
                    $lastPostsResultsKey++;
                    $lastPostsResultDay = $currentDay;
                    $lastPostsResults[$lastPostsResultsKey] = [
                        'date' => $lastPostsResultDay,
                        'posts' => []
                    ];
                }
                $lastPostsResults[$lastPostsResultsKey]['posts'][] = $post;
            }
        }
        
        $evolutionOfInteractions = FacebookPost::where(
            'facebook_analytics_id','=', $pageOverview->id)
        ->where(
            'facebook_created_at', '>=', date('Y-m-d', strtotime($now . "-7 days")))
        ->select(\DB::raw("DATE_FORMAT(facebook_created_at, '%Y-%m-%d') as date, sum(reaction_like + reaction_wow + reaction_angry + reaction_haha + reaction_wow + reaction_sad) as reactions,
        sum(comments) as comments, sum(shares) as shares"))->groupBy(\DB::raw("DATE_FORMAT(facebook_created_at, '%Y-%m-%d')"))->get();

        $result = [
            'page' => $pageOverview,
            'analytics' => [
                'growthFans' => $growthFans,
                'evolutionOfInteractions' => $evolutionOfInteractions,
                'bestPost' => $bestPost,
                'facebookLastPosts' => $lastPostsResults
            ]
        ];
        return response()->json($result, 200);
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
