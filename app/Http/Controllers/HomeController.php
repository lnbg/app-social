<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FacebookAnalytics;
use DateTime;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk as LaravelFacebookSDK;
use Goutte\Client;

class HomeController extends Controller
{
    public function index()
    {
        return view('facebook.index');
    }

    public function facebookFanpage()
    {
        $allFacebookAnalytics = FacebookAnalytics::all();
        return view('facebook.fanpage', ['allFacebookAnalytics' => $allFacebookAnalytics]);
    }
    
    public function facebookProfile()
    {
        return view('facebook.index');
    }
}
