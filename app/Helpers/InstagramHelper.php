<?php
namespace App\Helpers;

use App\Models\User;
use App\Models\InstagramAnalytics;
use App\Models\InstagramMedia;
use App\Models\InstagramFollower;
use DateTime;
use Smochin\Instagram\Crawler;

class InstagramHelper {
    
    /**
     * Smochin\Instagram\Crawler
     *
     * @var Smochin\Instagram\Crawler
     */
    private $instagramCrawler;

    public function __construct(Crawler $instagramCrawler)
    {
        $this->instagramCrawler = $instagramCrawler;
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

        $new = $this->getPostsOfInstagramByUserName($instagramAnalytics->user_name, $instagramAnalyticsID);
        InstagramMedia::insert($new);
        return $new;
    }

    /**
     * get all posts of instagram via user user
     *
     * @param [type] $username
     * @return void
     */
    public function getPostsOfInstagramByUserName($username, $instagram_analytics_id)
    {
        $allMedia = $this->instagramCrawler->getMediaByUser($username);
        $lastInstagramMedia = InstagramMedia::where('instagram_analytics_id', '=', $instagram_analytics_id)->orderBy('media_id', 'desc')->first();
        $lastInstagramMediaID = 0;
        if (isset($lastInstagramMedia)){
            $lastInstagramMediaID = $lastInstagramMedia->media_id;
        }
        $data = [];
        foreach ($allMedia as $media) 
        {
            if ($media->getId() > $lastInstagramMediaID) {
                $dimension = $media->getDimension();
                $dimension = 'w:' . $dimension->getWidth() . ',h:' .$dimension->getHeight();
                $tags = $media->getTags();
                $_tags = [];
                foreach ($tags as $tag) {
                    $_tags[] = $tag->getName();
                };
                $tags = implode(",", $_tags);
                $location = $media->getLocation();
                if (isset($location)) 
                {
                    $location = $location->getName();
                }
                $item = [
                    'media_id' => $media->getId(),
                    'instagram_analytics_id' => $instagram_analytics_id,
                    'media_type' => str_replace('Smochin\Instagram\Model\\','', get_class($media)),
                    'caption' => $media->getCaption(),
                    'url' => $media->getUrl(),
                    'dimension' => $dimension,
                    'likes' => $media->getLikesCount(),
                    'comments' => $media->getCommentsCount(),
                    'location' => $location,
                    'tags' => $tags,
                    'created_at' => date('Y-m-d H:s:i'),
                    'updated_at' => date('Y-m-d H:s:i'),
                ];
                $data[] = $item;
            }
        }
        return $data;
    }
}