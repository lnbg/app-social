<?php
namespace App\Helpers;

use App\Models\User;
use App\Models\FacebookAnalytics;
use App\Models\FacebookPost;
use DateTime;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk as LaravelFacebookSDK;
use Goutte\Client;
use GuzzleHttp\Client as Client2;

class FacebookHelper {
    
    static $ENDPOINT_READ_POSTS = '/posts';

    public function facebookFanpageGetPostByURI(LaravelFacebookSDK $laravelFacebookSDK, 
        $facebookAnalyticsID, $accessToken, $facebookPageName, &$analyticsData, &$postsStorage, $since)
    {
        try {
            // make default uri posts
            $defaultURI = '/' . $facebookPageName . self::$ENDPOINT_READ_POSTS;
            
            $posts = $laravelFacebookSDK->sendRequest('GET', $defaultURI, 
                ['limit' => 100, 'since' => $since, 
                'fields' => 'created_time, story, message, shares.summary(true), comments.summary(true), likes.summary(true), reactions.type(LOVE).limit(0).summary(1).as(loves), 
                reactions.type(HAHA).limit(0).summary(1).as(hahas), reactions.type(WOW).limit(0).summary(1).as(wows),
                reactions.type(THANKFUL).limit(0).summary(1).as(thankfuls),
                reactions.type(SAD).limit(0).summary(1).as(sads), reactions.type(ANGRY).limit(0).summary(1).as(angries)'], $accessToken);
            $posts = $posts->getGraphEdge();

            self::getMetaDataPostOfFanpage($laravelFacebookSDK, $facebookAnalyticsID, $accessToken, $posts, $analyticsData, $postsStorage);

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
                    $fetchStatus = self::getMetaDataPostOfFanpage($laravelFacebookSDK, $facebookAnalyticsID, $accessToken, $posts, $analyticsData, $postsStorage);
                    if ($fetchStatus == -1) {
                        $next = null;
                    }
                }
            }
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }
    }

    private static function getMetaDataPostOfFanpage(LaravelFacebookSDK $laravelFacebookSDK, $facebookAnalyticsID, $accessToken, $posts, 
        &$analyticsData, &$postsStorage) 
    {
        try {
            foreach ($posts as $item) {
                $analyticsData['total_posts']++;
                $likes = $item['likes'];
                $totalPostLikes = $likes->getMetaData()['summary']['total_count'];
                $analyticsData['total_posts_likes'] += $totalPostLikes;
                
                $comments = $item['comments'];
                $totalPostComments = $comments->getMetaData()['summary']['total_count'];
                $analyticsData['total_posts_comments'] += $totalPostComments;

                $totalPostShares = isset($item['shares']) ? $item['shares']['count'] : 0;
                $analyticsData['total_posts_shares'] += $totalPostShares;

                $hahas = $item['hahas'];
                $totalPostHahas = $hahas->getMetaData()['summary']['total_count'];
                $analyticsData['total_posts_hahas'] += $totalPostHahas;

                $wows = $item['wows'];
                $totalPostWows = $wows->getMetaData()['summary']['total_count'];
                $analyticsData['total_posts_wows'] += $totalPostWows;

                $sads = $item['sads'];
                $totalPostSads = $sads->getMetaData()['summary']['total_count'];
                $analyticsData['total_posts_sads'] += $totalPostSads;

                $thankfuls = $item['thankfuls'];
                $totalPostThankfuls = $thankfuls->getMetaData()['summary']['total_count'];
                $analyticsData['total_posts_thankfuls'] += $totalPostThankfuls;

                $loves = $item['loves'];
                $totalPostLoves = $loves->getMetaData()['summary']['total_count'];;
                $analyticsData['total_posts_loves'] += $totalPostLoves;

                $angries = $item['angries'];
                $totalPostAngries = $angries->getMetaData()['summary']['total_count'];
                $analyticsData['total_posts_angries'] += $totalPostAngries;

                $dbItem = [
                    'facebook_analytics_id' => $facebookAnalyticsID,
                    'facebook_post_id' => $item['id'],
                    'messages' => isset($item['message']) ? $item['message'] : '',
                    'story' => isset($item['story']) ? $item['story'] : '',
                    'comments' => $totalPostComments,
                    'shares' => $totalPostShares,
                    'reaction_like' => $totalPostLikes,
                    'reaction_haha' => $totalPostHahas,
                    'reaction_wow' => $totalPostWows,
                    'reaction_love' => $totalPostLoves,
                    'reaction_sad' => $totalPostSads,
                    'reaction_angry' => $totalPostAngries,
                    'facebook_created_at' => $item['created_time'], 
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ];

                $postsStorage[] = $dbItem;
            }
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            return -1;
        }
    }

    public function getPageLikes(LaravelFacebookSDK $laravelFacebookSDK, $accessToken, $facebookPageName)
    {
        try {
            $defaultURIpageLikeFacebookAPI = '/' . $facebookPageName . '?fields=fan_count';
            $likes = $laravelFacebookSDK->get($defaultURIpageLikeFacebookAPI, $accessToken);
            return $likes->getDecodedBody()['fan_count'];
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            return 0;
        }
    }
}