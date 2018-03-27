<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Smochin\Instagram\Crawler;

class InstagramAnalyticsController extends Controller
{
    /**
     * Facebook Helpers
     *
     * @var Crawler
     */
    protected $instagramCrawler;
    
    public function __construct(Crawler $instagramCrawler) 
    {
        $this->instagramCrawler = $instagramCrawler;
    }

    public function debug()
    {
        //$user = $this->instagramCrawler->getUser('mytam.info');
        //$media = $this->instagramCrawler->getMediaByUser('mytam.info');
    }
}
