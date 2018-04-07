<?php
namespace App\Helpers;

use App\Models\User;
use App\Models\InstagramAnalytics;
use App\Models\InstagramMedia;
use App\Models\InstagramFollower;
use DateTime;
use Smochin\Instagram\Crawler;
use InstagramScraper\Instagram as InstagramScrapper;

class InstagramHelper {
    
    /**
     * Smochin\Instagram\Crawler
     *
     * @var Smochin\Instagram\Crawler
     */
    private $instagramCrawler;
    private $instagramScrapper;

    public function __construct(Crawler $instagramCrawler, InstagramScrapper $instagramScrapper)
    {
        $this->instagramCrawler = $instagramCrawler;
        // $this->instagramScrapper = $instagramScrapper;
        $this->instagramScrapper =  InstagramScrapper::withCredentials('yukign2804', 'Hoaanhdao2804!', '/Users/deveio/Projects/bot-social/storage/logs/');
        $this->instagramScrapper->login();
    }

    /**
     * Get instagram information via username
     *
     * @param string $username
     * @return array
     */
    public function getInstagramInfoViaUsername($username)
    {
        $info = $this->instagramCrawler->getUser($username);
        $profile = $info->getProfile();
        $data = [
            'instagram_id' => $info->getId(),
            'name' => $info->getName(),
            'user_name' => $info->getUsername(),
            'picture' => $info->getPicture(),
            'website' => $profile->getWebsite(),
            'followers_count' => $profile->getFollowersCount(),
            'follows_count' => $profile->getFollowsCount(),
            'media_counts' => $profile->getMediaCount(),
            'biography' => $profile->getBiography()
        ];
        return $data;
    }

    /**
     * analytics Instagram Profile via instagram analytics id
     *
     * @param [type] $instagramAnalyticsID
     * @return void
     */
    public function analyticsInstagramProfile($instagramAnalyticsID) 
    {
        $instagramAnalytics = InstagramAnalytics::find($instagramAnalyticsID);
        $instagramInfo = $this->getInstagramInfoViaUsername($instagramAnalytics->user_name);
        $instagramAnalytics->followers_count = $instagramInfo['followers_count'];
        $instagramAnalytics->picture = $instagramInfo['picture'];
        $instagramAnalytics->follows_count = $instagramInfo['follows_count'];
        $instagramAnalytics->media_counts = $instagramInfo['media_counts'];
        $instagramAnalytics->biography = $instagramInfo['biography'];
        $instagramAnalytics->save();

        $existInstagramFollowerInday = InstagramFollower::where('instagram_analytics_id', '=', $instagramAnalyticsID)
            ->where('date_sync', '=', date('Y-m-d'))->first();

        if (isset($existInstagramFollowerInday)) {
            $existInstagramFollowerInday->instagram_followers = $instagramInfo['followers_count'];
            $existInstagramFollowerInday->updated_at = date('Y-m-d H:s:i');
            $existInstagramFollowerInday->save();
        } else {
            InstagramFollower::insert([
                'instagram_analytics_id' => $instagramAnalyticsID,
                'instagram_followers' => $instagramInfo['followers_count'],
                'date_sync' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:s:i'),
                'updated_at' => date('Y-m-d H:s:i'),
            ]);
        }
        // update again likes and comments count for each media
        $this->updateReactionMediaByMediaID($instagramAnalytics->user_name, $instagramAnalyticsID);
        // insert new media
        $new = $this->getPostsOfInstagramByUserName($instagramAnalytics->user_name, $instagramAnalyticsID);
        InstagramMedia::insert($new);
        return $new;
    }

    public function updateReactionMediaByMediaID($username, $instagram_analytics_id)
    {
        $allMedia = InstagramMedia::where('instagram_analytics_id', '=', $instagram_analytics_id);
        foreach ($allMedia as $media) {
            try {
                $_media = $this->instagramCrawler->getMedia($media->getShortCode());
                $media->likes = $_media->getLikesCount();
                $media->comments = $_media->getCommentsCount();
                $media->media_picture = $_media->getUrl();
                $media->save();
            }
            catch (\Exception $e) {
                continue;
            }
        }
    }

    /**
     * get new posts of instagram via user name
     *
     * @param [type] $username
     * @return void
     */
    public function getPostsOfInstagramByUserName($username, $instagram_analytics_id)
    {
        $allMedia = $this->instagramScrapper->getMedias($username, 10);
        $lastInstagramMedia = InstagramMedia::where('instagram_analytics_id', '=', $instagram_analytics_id)->orderBy('media_id', 'desc')->first();
        $lastInstagramMediaID = 0;
        if (isset($lastInstagramMedia)){
            $lastInstagramMediaID = $lastInstagramMedia->media_id;
        }
        $data = [];
        foreach ($allMedia as $media) 
        {
            if ($media->getId() > $lastInstagramMediaID) {
                $_media = $this->instagramCrawler->getMedia($media->getShortCode());
                $dimension = $_media->getDimension();
                $dimension = 'w:' . $dimension->getWidth() . ',h:' .$dimension->getHeight();
                $tags = $_media->getTags();
                $_tags = [];
                foreach ($tags as $tag) {
                    $_tags[] = $tag->getName();
                };
                $tags = implode(",", $_tags);
                $location = $_media->getLocation();
                if (isset($location)) 
                {
                    $location = $location->getName();
                }
                $item = [
                    'media_id' => $media->getId(),
                    'instagram_analytics_id' => $instagram_analytics_id,
                    'media_type' => $media->getType(),
                    'media_code' => $media->getShortCode(),
                    'caption' => $media->getCaption(),
                    'url' => $media->getLink(),
                    'media_picture' => $_media->getUrl(),
                    'dimension' => $dimension,
                    'location' => $location,
                    'tags' => $tags,
                    'likes' => $media->getLikesCount(),
                    'comments' => $media->getCommentsCount(),
                    'instagram_created_at' => $_media->getCreated()->format('Y-m-d H:s:i'),
                    'created_at' => date('Y-m-d H:s:i'),
                    'updated_at' => date('Y-m-d H:s:i'),
                ];
                $data[] = $item;
            }
        }
        return $data;
    }

    /**
     * Init analytics instagram with 100 media lastest
     *
     * @param [type] $id
     * @return void
     */
    public function initAnalyticsInstagramMediaByID($id) {
        $instagram = InstagramAnalytics::find($id);
        $instagramInfo = $this->getInstagramInfoViaUsername($instagram->user_name);
        $instagram->followers_count = $instagramInfo['followers_count'];
        $instagram->picture = $instagramInfo['picture'];
        $instagram->follows_count = $instagramInfo['follows_count'];
        $instagram->media_counts = $instagramInfo['media_counts'];
        $instagram->biography = $instagramInfo['biography'];
        $instagram->save();

        $existInstagramFollowerInday = InstagramFollower::where('instagram_analytics_id', '=', $id)
            ->where('date_sync', '=', date('Y-m-d'))->first();

        if (isset($existInstagramFollowerInday)) {
            $existInstagramFollowerInday->instagram_followers = $instagramInfo['followers_count'];
            $existInstagramFollowerInday->updated_at = date('Y-m-d H:s:i');
            $existInstagramFollowerInday->save();
        } else {
            InstagramFollower::insert([
                'instagram_analytics_id' => $id,
                'instagram_followers' => $instagramInfo['followers_count'],
                'date_sync' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:s:i'),
                'updated_at' => date('Y-m-d H:s:i'),
            ]);
        }
        $allMedia = $this->instagramScrapper->getMedias($instagram->user_name, 100);
        foreach ($allMedia as $media) {
            $_media = $this->instagramCrawler->getMedia($media->getShortCode());
            $dimension = $_media->getDimension();
            $dimension = 'w:' . $dimension->getWidth() . ',h:' .$dimension->getHeight();
            $tags = $_media->getTags();
            $_tags = [];
            foreach ($tags as $tag) {
                $_tags[] = $tag->getName();
            };
            $tags = implode(",", $_tags);
            $location = $_media->getLocation();
            if (isset($location)) 
            {
                $location = $location->getName();
            }
            $item = [
                'media_id' => $media->getId(),
                'instagram_analytics_id' => $instagram->id,
                'media_type' => $media->getType(),
                'media_code' => $media->getShortCode(),
                'caption' => $media->getCaption(),
                'url' => $media->getLink(),
                'media_picture' => $_media->getUrl(),
                'dimension' => $dimension,
                'location' => $location,
                'tags' => $tags,
                'likes' => $media->getLikesCount(),
                'comments' => $media->getCommentsCount(),
                'instagram_created_at' => $_media->getCreated()->format('Y-m-d H:s:i'),
                'created_at' => date('Y-m-d H:s:i'),
                'updated_at' => date('Y-m-d H:s:i'),
            ];
            $data[] = $item;
        }
        InstagramMedia::insert($data);
    }
}