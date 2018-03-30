<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Smochin\Instagram\Crawler;
use App\Helpers\InstagramHelper;

use App\Models\InstagramAnalytics;
use App\Models\InstagramMedia;
use App\Models\InstagramFollower;

class InstagramAnalyticsController extends Controller
{
    /**
     * Facebook Helpers
     *
     * @var Crawler
     */
    protected $instagramCrawler;

    protected $instagramHelper;
    
    public function __construct(Crawler $instagramCrawler, InstagramHelper $instagramHelper) 
    {
        $this->instagramCrawler = $instagramCrawler;
        $this->instagramHelper = $instagramHelper;
    }

    /**
     * get all records of table instagram_analytics
     */
    public function getListInstagramProfilesAnalytics()
    {
        $listInstagramAnalytics = InstagramAnalytics::all();
        return response()->json($listInstagramAnalytics, 200);
    }

    public function getInstagramProfileByInstagramAnalyticsID(Request $request)
    {
        $instagramProfile = InstagramAnalytics::where('user_name', '=', $request->username)->first();
        $growthFans = InstagramFollower::where('instagram_analytics_id', '=', $instagramProfile->id)->select('instagram_followers', 'date_sync')->get();
        $instagramTotalMediaPerDay = InstagramMedia::where('instagram_analytics_id', '=', $instagramProfile->id)->select(\DB::raw("date_format(instagram_created_at, '%Y-%m-%d') as date, count(media_id) as value"))
            ->groupBy(\DB::raw("date_format(instagram_created_at, '%Y-%m-%d')"))->get();

        $instagramTotalMediaGroupByType = InstagramMedia::where('instagram_analytics_id', '=', $instagramProfile->id)
            ->select(\DB::raw('media_type, count(media_id) as value'))->groupBy('media_type')->get();
            
        $instagramLastMedias = InstagramMedia::where(
            'instagram_analytics_id','=', $instagramProfile->id)
            ->orderBy('instagram_created_at', 'desc')->limit(6)->get();
        $data = [
            'profile' => $instagramProfile,
            'analytics' => [
                'growthFans' => $growthFans,
                'instagramTotalMediaPerDay' => $instagramTotalMediaPerDay,
                'instagramTotalMediaGroupByType' => $instagramTotalMediaGroupByType,
                'instagramLastMedias' => $instagramLastMedias
            ]
        ];
        return response()->json($data, 200);
    }

    public function createNewInstagramProfile(Request $request)
    {
        try {
            $instagramFanpageLink = $request->instagram_link;
            $instagramFanpageLink = explode('/', $instagramFanpageLink);
            $instagramProfileUsername = $instagramFanpageLink[3];
            $data = $this->instagramHelper->getInstagramInfoViaUsername($instagramProfileUsername);
            $new = InstagramAnalytics::create([
                'instagram_id' => $data['instagram_id'],
                'user_name' => $data['user_name'],
                'name' => $data['name'],
                'picture' => $data['picture'],
                'link' => $request->instagram_link,
                'website' => $data['website'],
                'followers_count' => $data['followers_count'],
                'follows_count' => $data['follows_count'],
                'media_counts' => $data['media_counts'],
                'biography' => $data['biography'] 
            ]);
            return response()->json($new, 200);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function analyticsInstagramProfile(Request $request)
    {
        try {
            $instagramAnalyticsID = $request->id;
            $new = $this->instagramHelper->analyticsInstagramProfile($instagramAnalyticsID);
            return response()->json($new, 200);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
