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

    public function getFacebookAccessToken() {
        return User::where('type', '=', 1)->first()->access_token;
    }

    /**
     * analytics new posts from facebook graph api
     *
     * @param LaravelFacebookSDK $laravelFacebookSDK
     * @param [type] $facebookAnalyticsID
     * @param [type] $accessToken
     * @param [type] $facebookPageName
     * @param [type] $analyticsData
     * @param [type] $postsStorage
     * @param [type] $since
     * @return void
     */
    public function facebookFanpageGetPostByURI(LaravelFacebookSDK $laravelFacebookSDK, 
        $facebookAnalyticsID, $accessToken, $facebookPageName, &$postsStorage, $since)
    {
        try {
            // get last post by facebook_post_id
            $lastPost = FacebookPost::where('facebook_analytics_id', '=', $facebookAnalyticsID)->orderBy('facebook_post_id', 'desc')->limit(1)->first();
            $lastPostId = isset($lastPost) ? $lastPost->facebook_post_id : 0;
            // make default uri posts
            $defaultURI = '/' . $facebookPageName . self::$ENDPOINT_READ_POSTS;
            // send a request as graph facebook api 
            $posts = $laravelFacebookSDK->sendRequest('GET', $defaultURI, 
                ['limit' => 100, 'since' => $since, 
                'fields' => 'created_time, story, message, type, shares.summary(true), comments.summary(true), likes.summary(true), reactions.type(LOVE).limit(0).summary(1).as(loves), 
                reactions.type(HAHA).limit(0).summary(1).as(hahas), reactions.type(WOW).limit(0).summary(1).as(wows),
                reactions.type(THANKFUL).limit(0).summary(1).as(thankfuls),
                reactions.type(SAD).limit(0).summary(1).as(sads), reactions.type(ANGRY).limit(0).summary(1).as(angries)'], $accessToken);
            // get post from above request
            $posts = $posts->getGraphEdge();
            // get meta data of posts
            self::getMetaDataPostOfFanpage($laravelFacebookSDK, $facebookAnalyticsID, $accessToken, $posts, $postsStorage, $lastPostId);
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
                    $fetchStatus = self::getMetaDataPostOfFanpage($laravelFacebookSDK, $facebookAnalyticsID, $accessToken, $posts, $postsStorage, $lastPostId);
                    if ($fetchStatus == -1) {
                        $next = null;
                    }
                }
            }
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            
        }
    }

    /**
     * Analytics when update olds record in facebook_posts table
     *
     * @param LaravelFacebookSDK $laravelFacebookSDK
     * @param [type] $facebookAnalyticsID
     * @param [type] $accessToken
     * @return void
     */
    public function facebookFanpageUpdateEachPostByAnalyticsID(LaravelFacebookSDK $laravelFacebookSDK, $accessToken, $facebookAnalyticsID)
    {
        $allPosts = FacebookPost::where('facebook_analytics_id', '=', $facebookAnalyticsID)->get();
        foreach ($allPosts as  $oldPost)  {
            try {
                $defaultURI = '/' . $oldPost->facebook_post_id;
                $postFromAPI = $laravelFacebookSDK->sendRequest('GET', $defaultURI, 
                    ['fields' => 'type, shares.summary(true), comments.summary(true), likes.summary(true), reactions.type(LOVE).limit(0).summary(1).as(loves), 
                    reactions.type(HAHA).limit(0).summary(1).as(hahas), reactions.type(WOW).limit(0).summary(1).as(wows),
                    reactions.type(THANKFUL).limit(0).summary(1).as(thankfuls),
                    reactions.type(SAD).limit(0).summary(1).as(sads), reactions.type(ANGRY).limit(0).summary(1).as(angries)'], $accessToken);
                // get post from above request
                $postFromAPI = $postFromAPI->getGraphNode();
                // get meta data of posts
                self::getMetaDataEachPostOfFanpage($laravelFacebookSDK, $postFromAPI, $oldPost->id);
            } catch (Facebook\Exceptions\FacebookSDKException $ex) {
                \Log::error("facebookFanpageUpdateEachPostByAnalyticsID:FB:Post-" . $oldPost->facebook_post_id . " cannot update");
                continue;
            } catch (\Exception $ex) {
                \Log::error("facebookFanpageUpdateEachPostByAnalyticsID:Post-" . $oldPost->facebook_post_id . " cannot update");
                continue;
            }
        }
    }

    /**
     * get meta data of new post
     *
     * @param LaravelFacebookSDK $laravelFacebookSDK
     * @param [type] $facebookAnalyticsID
     * @param [type] $accessToken
     * @param [type] $posts
     * @param [type] $postsStorage
     * @return void
     */
    private static function getMetaDataPostOfFanpage(LaravelFacebookSDK $laravelFacebookSDK, $facebookAnalyticsID, $accessToken, $posts, &$postsStorage, $lastPostId) 
    {
        foreach ($posts as $item) {
            if ($item['id'] > $lastPostId) {
                try {
                    $likes = $item['likes'];
                    $likes = $likes->getMetaData()['summary']['total_count'];
                    
                    $comments = $item['comments'];
                    $comments = $comments->getMetaData()['summary']['total_count'];

                    $shares = isset($item['shares']) ? $item['shares']['count'] : 0;
                    $type = $item['type'];

                    $hahas = $item['hahas'];
                    $hahas = $hahas->getMetaData()['summary']['total_count'];

                    $wows = $item['wows'];
                    $wows = $wows->getMetaData()['summary']['total_count'];

                    $sads = $item['sads'];
                    $sads = $sads->getMetaData()['summary']['total_count'];

                    $thankfuls = $item['thankfuls'];
                    $thankfuls = $thankfuls->getMetaData()['summary']['total_count'];

                    $loves = $item['loves'];
                    $loves = $loves->getMetaData()['summary']['total_count'];;

                    $angries = $item['angries'];
                    $angries = $angries->getMetaData()['summary']['total_count'];

                    $picture = $laravelFacebookSDK->sendRequest('GET', '/' . $item['id'],['fields' => 'full_picture'], $accessToken);
                    $picture = $picture->getDecodedBody();
                    if (isset($picture['full_picture'])) {
                        $picture = $picture['full_picture'];
                    } else {
                        $picture = '';
                    }

                    $dbItem = [
                        'facebook_analytics_id' => $facebookAnalyticsID,
                        'facebook_post_id' => $item['id'],
                        'messages' => isset($item['message']) ? $item['message'] : '',
                        'picture' => $picture,
                        'story' => isset($item['story']) ? $item['story'] : '',
                        'comments' => $comments,
                        'shares' => $shares,
                        'type' => $type,
                        'reaction_like' => $likes,
                        'reaction_haha' => $hahas,
                        'reaction_wow' => $wows,
                        'reaction_love' => $loves,
                        'reaction_sad' => $sads,
                        'reaction_angry' => $angries,
                        'reaction_thankful' => $thankfuls,
                        'facebook_created_at' => $item['created_time'], 
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s")
                    ];
                    $postsStorage[] = $dbItem;
                } catch (Facebook\Exceptions\FacebookSDKException $ex) {
                    continue;
                } catch (\Exception $ex) {
                    continue;
                }
            }
        }
        
    }

    /**
     * get meta data of post when update
     *
     * @param LaravelFacebookSDK $laravelFacebookSDK
     * @param [type] $post
     * @return void
     */
    private static function getMetaDataEachPostOfFanpage(LaravelFacebookSDK $laravelFacebookSDK, $postFromAPI, $postID) 
    {
        try {
            $likes = $postFromAPI['likes'];
            $reaction_like = $likes->getMetaData()['summary']['total_count'];
            
            $comments = $postFromAPI['comments'];
            $comments = $comments->getMetaData()['summary']['total_count'];

            $shares = isset($postFromAPI['shares']) ? $postFromAPI['shares']['count'] : 0;
            $type = $postFromAPI['type'];

            $hahas = $postFromAPI['hahas'];
            $reaction_haha = $hahas->getMetaData()['summary']['total_count'];

            $wows = $postFromAPI['wows'];
            $reaction_wow = $wows->getMetaData()['summary']['total_count'];

            $sads = $postFromAPI['sads'];
            $reaction_sad = $sads->getMetaData()['summary']['total_count'];

            $thankfuls = $postFromAPI['thankfuls'];
            $reaction_thankful = $thankfuls->getMetaData()['summary']['total_count'];

            $loves = $postFromAPI['loves'];
            $reaction_love = $loves->getMetaData()['summary']['total_count'];;

            $angries = $postFromAPI['angries'];
            $reaction_angry = $angries->getMetaData()['summary']['total_count'];
            
            FacebookPost::find($postID)->update([
                'reaction_like' => $reaction_like,
                'comments' => $comments,
                'shares' => $shares,
                'type' => $type,
                'reaction_haha' => $reaction_haha,
                'reaction_love' => $reaction_love,
                'reaction_wow' => $reaction_wow,
                'reaction_sad' => $reaction_sad,
                'reaction_thankful' => $reaction_thankful,
                'reaction_angry' => $reaction_angry,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            return true;
        } catch (Facebook\Exceptions\FacebookSDKException $ex) {
            \Log::error("getMetaDataEachPostOfFanpageFacebookSDKException:Post-" . $postID . " cannot update");
            return false;
        } catch (\Exception $ex) {
            \Log::error("getMetaDataEachPostOfFanpage:Post-" . $postID . " cannot update");
            return false;
        }
    }

    public function getPageLikes(LaravelFacebookSDK $laravelFacebookSDK, $accessToken, $facebookPageName)
    {
        try {
            $defaultURIpageLikeFacebookAPI = '/' . $facebookPageName . '?fields=fan_count';
            $likes = $laravelFacebookSDK->get($defaultURIpageLikeFacebookAPI, $accessToken);
            return $likes->getDecodedBody()['fan_count'];
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            return false;
        } catch (\Exception $ex) {
            return false;
        }
    }
}