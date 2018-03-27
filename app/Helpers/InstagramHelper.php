<?php
namespace App\Helpers;

use App\Models\User;
use App\Models\InstagramAnalytics;
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
            'profile' => [
                'website' => $profile->getWebsite(),
                'followers_count' => $profile->getFollowersCount(),
                'follows_count' => $profile->getFollowsCount(),
                'media_counts' => $profile->getMediaCount(),
                'biography' => $profile->getBiography()
            ]
        ];
        return $data;
    }

    /**
     * get all posts of instagram via user user
     *
     * @param [type] $username
     * @return void
     */
    public function getPostsOfInstagramByUserName($username)
    {
        $allMedia = $this->instagramCrawler->getMediaByUser($username);
        $data = [];
        foreach ($allMedia as $media) 
        {
            $demension = $media->getDimension();
            $item = [
                'id' => $media->getId(),
                'caption' => $media->getCaption(),
                'url' => $media->getUrl(),
                'demension' => [
                    'w' => $demension->getWidth(),
                    'h' => $demension->getHeight()
                ],
                'likes' => $media->getLikesCount(),
                'comments' => $media->getCommentsCount(),
            ];
            $data[] = $item;
        }
        return $data;
    }
}