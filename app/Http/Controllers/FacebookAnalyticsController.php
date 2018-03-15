<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FacebookAnalytics;
use DateTime;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk as LaravelFacebookSDK;
use Goutte\Client;
use GuzzleHttp\Client as Client2;

class FacebookAnalyticsController extends Controller
{
    /**
     * get all records of table facebook_analytics
     */
    public function getListFacebookPageAnalytics()
    {
        $listFacebookPageAnalytics = FacebookAnalytics::all();
        return response()->json($listFacebookPageAnalytics, 200);
    }
    
    public function addNewFacebookPage(LaravelFacebookSDK $laravelFacebookSDK, Request $request)
    {
        try {
            $accessToken = $user = User::first()->access_token;
            $facebookFanpageLink = $request->page_link;
            $facebookFanpageLink = explode('/', $facebookFanpageLink);
            $facebookFanpageName = $facebookFanpageLink[3];
            $defaultURI = '/' . $facebookFanpageName;
            $posts = $laravelFacebookSDK->sendRequest('GET', $defaultURI, ['fields' => 'name,picture'], $accessToken);
            $posts = $posts->getDecodedBody();
            FacebookAnalytics::create([
                'account_id' => $posts['id'],
                'account_name' => $posts['name'],
                'account_link' => $request->page_link,
                'account_picture' => $posts['picture']['data']['url']
            ]);
            return redirect('/facebook/fanpage');
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
        
    }

    public function analyticsFacebookPage(LaravelFacebookSDK $laravelFacebookSDK, Request $request)
    {
        try {
            // get request data
            $user = User::first(); // get user to get access token
            $id = $request->id;
            // Meta data
            $facebookAnalytics = FacebookAnalytics::find($id);
            $now = date('Y-m-d');
            $effectiveDate = date('Y-m-d', strtotime($now . "-3 months") );
            //retrive yesterday's date in the format 9999-99-99
            $facebookFanpageLink = explode('/', $facebookAnalytics->account_link);
            $facebookPageName = $facebookFanpageLink[3];
            $posts = [];
            $totalPosts = 0;
            $accountActivedDate = date('Y-m-d');
            $totalPostsLikes = 0;
            $totalPostsComments = 0;
            $totalPageFollowers = 0;
            $totalPageLikes = 0;
            // get followers
            $totalPageFollowers = 0;
            $client = new Client();
            $crawler = $client->request('GET', 'https://www.facebook.com/' . $facebookPageName);
            $nodeFollowers = $crawler->filter('div._4bl9')->eq(2)->extract(array('_text', 'class', 'href'));
            $totalPageFollowers = intval(preg_replace( '/[^0-9]/', '', $nodeFollowers[0][0]));
            $now = date('Y-m-d');
            $dteStart = new DateTime($now);
            self::facebookFanpageGetPostByURI($laravelFacebookSDK, $user->access_token, $facebookPageName, $posts, 
                null, $totalPosts, $totalPostsLikes, $totalPostsComments, $effectiveDate);
            $allKeyOfData = array_keys($posts);
            $accountActivedDate = end($allKeyOfData);
            $dteEnd = new DateTime($accountActivedDate);
            $diffDays = $dteStart->diff($dteEnd)->days;
            // get page likes
            $totalPageLikes = self::getPageLikes($laravelFacebookSDK, $user->access_token, $facebookPageName);
            $response = [
                'name' => $facebookPageName,
                'accountActivedDate' => $accountActivedDate,
                'totalPageLikes' => $totalPageLikes,
                'totalFollowers' => $totalPageFollowers,
                'totalPosts' => $totalPosts,
                'totalDays' => $diffDays,
                'totalPostsLikes' => $totalPostsLikes,
                'totalPostsComments' => $totalPostsComments,
                'averagePostsPerDay' => $totalPosts / $diffDays,
                'averageLikesPerPost' => intval($totalPostsLikes / $diffDays),
                'averageCommentsPerPost' => intval($totalPostsComments / $diffDays)
            ];
            $facebookAnalytics->account_created_date = $accountActivedDate;
            $facebookAnalytics->total_likes = $totalPageLikes;
            $facebookAnalytics->total_posts = $totalPosts;
            $facebookAnalytics->total_followers = $totalPageFollowers;
            $facebookAnalytics->total_days = $diffDays;
            $facebookAnalytics->total_likes_posts = $totalPostsLikes;
            $facebookAnalytics->average_posts_per_day = round($totalPosts / $diffDays, 2);
            $facebookAnalytics->average_likes_per_post = round($totalPostsLikes / $diffDays, 2);
            $facebookAnalytics->average_comments_per_post = round($totalPostsComments / $diffDays, 2);
            $facebookAnalytics->save();
            return response()->json($facebookAnalytics, 200);
        } catch (\Exception $e) {
            return response()->json($facebookAnalytics, 500);
        }
    }

    public function debug(LaravelFacebookSDK $laravelFacebookSDK, Request $request)
    {
        // $client = new Client2(['base_uri' => 'https://www.facebook.com/']);
        // $response = $client->request('GET', 'mytamsinger1981');
        // $body = $response->getBody();
        // $stringBody = (string) $body;
        // dd($stringBody);
        //https://graph.facebook.com/v2.6/pagename/insights/page_fans_country/lifetime?&since=yyy-mm-dd&until=yyyy-mm-dd&access_token=xxx
        // make default uri posts
        $user = User::first();
        $defaultURI = '/1124060737634509/insights';
        $posts = $laravelFacebookSDK->sendRequest('GET', $defaultURI, 
        ['limit' => 100, 'since' => '2018-02-12', 'until' => '2018-03-12'], $user->access_token);
        dd($posts);
    }

    private static function facebookFanpageGetPostByURI(LaravelFacebookSDK $laravelFacebookSDK, 
        $accessToken, $facebookPageName, &$data, $currentKey, 
        &$totalPosts, &$totalPostsLikes, &$totalPostsComments, $since)
    {
        try {
            // make default uri posts
            $defaultURI = '/' . $facebookPageName . '/posts';
            
            $posts = $laravelFacebookSDK->sendRequest('GET', $defaultURI, 
                ['limit' => 100, 'since' => $since, 
                'fields' => 'created_time, comments.summary(true),likes.summary(true)'], $accessToken);
            $posts = $posts->getGraphEdge();

            self::getMetaDataPostOfFanpage($laravelFacebookSDK, $accessToken, $posts, $data,
                $currentKey, $totalPosts, $totalPostsLikes, $totalPostsComments);

            if (isset($posts->getMetaData()['paging']['next'])) {
                $next = $posts->getMetaData()['paging']['next'];
            }

            while (isset($next)) {
                $posts = $laravelFacebookSDK->next($posts);
                if (isset($posts->getMetaData()['paging']['next'])) {
                    $next = $posts->getMetaData()['paging']['next'];
                } else {
                    $next = null;
                }
                if (isset($posts) && count($posts) > 0) {
                    $fetchStatus = self::getMetaDataPostOfFanpage($laravelFacebookSDK, $accessToken, $posts, $data,
                        $currentKey, $totalPosts, $totalPostsLikes, $totalPostsComments);
                    if ($fetchStatus == -1) {
                        $next = null;
                    }
                }
            }
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }
    }

    private static function getMetaDataPostOfFanpage(LaravelFacebookSDK $laravelFacebookSDK, $accessToken, $posts, 
        &$data, &$currentKey, &$totalPosts, &$totalPostsLikes, &$totalPostsComments) 
    {
        try {
            foreach ($posts as $item) {
                $totalPosts++;
                $likes = $item['likes'];
                $totalPostsLikes += $likes->getMetaData()['summary']['total_count'];

                $comments = $item['comments'];
                $totalPostsComments += $comments->getMetaData()['summary']['total_count'];

                $date = json_decode(json_encode($item['created_time']))->date;
                if (isset($currentKey)) {
                    $newKey = date("Y-m-d", strtotime($date));
                    if ($newKey != $currentKey) {
                        $data[$newKey] = 1;
                        $currentKey = $newKey;
                    } else {
                        $data[$currentKey]++;
                    }
                } else {
                    $newKey = date("Y-m-d", strtotime($date));
                    $data[$newKey] = 1;
                    $currentKey = $newKey;
                }
            }
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            return -1;
        }
    }

    private static function getPageLikes(LaravelFacebookSDK $laravelFacebookSDK, $accessToken, $facebookPageName)
    {
        $defaultURIpageLikeFacebookAPI = '/' . $facebookPageName . '?fields=fan_count';
        $likes = $laravelFacebookSDK->get($defaultURIpageLikeFacebookAPI, $accessToken);
        return $likes->getDecodedBody()['fan_count'];
    }
}
