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
use App\Jobs\FetchFacebookData;

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

    public function reloadQueue() 
    {
        $user = User::where('type', '=', 2)->first();
        $accessToken = $user->access_token;
        $facebookAnalytics = FacebookAnalytics::all();
        foreach ($facebookAnalytics as $page) {
            FetchFacebookData::dispatch($page, $accessToken);
        }
    }

    public function createNewFacebookPage(LaravelFacebookSDK $laravelFacebookSDK, Request $request)
    {
        try {
            $accessToken = User::where('type', '=', 2)->first()->access_token;
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
            return response()->json($new, 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

    }
    /**
     * analytics data for facebook page
     *
     * @param LaravelFacebookSDK $laravelFacebookSDK
     * @param Request $request
     * @return void
     */
    public function analyticsFacebookPage(LaravelFacebookSDK $laravelFacebookSDK, Request $request)
    {
        // get request data
        $user = User::where('type', '=', 2)->first(); // get user to get access token
        $id = $request->id; // id of facebook page
        // get facebook analytics object by id
        $facebookAnalytics = FacebookAnalytics::find($id);
        // get since
        $now = date('Y-m-d');
        $effectiveDate = date('Y-m-d', strtotime($now . "-1 years"));
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
        $facebookAnalytics->total_days = 365;
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
        $facebookAnalytics->average_posts_per_day = round($facebookAnalytics->total_posts / $facebookAnalytics->total_days, 2);
        $facebookAnalytics->average_reactions_per_post = round($reactions / $facebookAnalytics->total_days, 2);
        $facebookAnalytics->average_interactions_per_post = round($interactions / $facebookAnalytics->total_days, 2);

        $facebookAnalytics->save();
        return response()->json($facebookAnalytics, 200);
    }

    public function getFacebookPageByFacebookAnalyticID(Request $request)
    {
        $now = date('Y-m-d');

        $pageOverview = FacebookAnalytics::where('account_username', '=', $request->username)->first();
        $growthFans = FacebookFan::where('facebook_analytics_id', '=', $pageOverview->id)->select('facebook_fans', 'date_sync')->get();

        $facebookDistributionOfPostType = FacebookPost::where(
            'facebook_analytics_id', '=', $pageOverview->id
        )->select(\DB::raw("count(facebook_post_id) as value, facebook_posts.type"))->groupBy('facebook_posts.type')->get();

        $facebookDistributionOfInteraction = FacebookPost::where('facebook_analytics_id', '=', $pageOverview->id)
        ->select(\DB::raw("sum(reaction_like + reaction_wow + reaction_angry + reaction_haha + reaction_wow + reaction_sad) as reactions, sum(shares) as shares, sum(comments) as comments"))
        ->first();

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
            ->where('facebook_created_at', '>=', date('Y-m-d', strtotime($now . "-1 months")))
            ->select(\DB::raw("DATE_FORMAT(facebook_created_at, '%Y-%m-%d') as date, sum(reaction_like + reaction_wow + reaction_angry + reaction_haha + reaction_wow + reaction_sad) as reactions,
        sum(comments) as comments, sum(shares) as shares"))->groupBy(\DB::raw("DATE_FORMAT(facebook_created_at, '%Y-%m-%d')"))->get();

        $result = [
            'page' => $pageOverview,
            'analytics' => [
                'growthFans' => $growthFans,
                'evolutionOfInteractions' => $evolutionOfInteractions,
                'bestPost' => $bestPost,
                'facebookLastPosts' => $lastPostsResults,
                'facebookDistributionOfInteraction' => $facebookDistributionOfInteraction,
                'facebookDistributionOfPostType' => $facebookDistributionOfPostType
            ]
        ];
        return response()->json($result, 200);
    }

    public function debug(LaravelFacebookSDK $laravelFacebookSDK, Request $request)
    {
        $user = User::where('type', '=', 2)->first();
        $defaultURI = '/1124060737634509/insights';
        $posts = $laravelFacebookSDK->sendRequest('GET', $defaultURI,
        ['limit' => 100, 'since' => '2018-02-12', 'until' => '2018-03-12'], $user->access_token);
        dd($posts);
    }
}
