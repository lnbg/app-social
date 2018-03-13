<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FacebookAnalytics;
use DateTime;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk as LaravelFacebookSDK;
use Goutte\Client;

class UserController extends Controller
{
    public function setup(LaravelFacebookSDK $laravelFacebookSDK) 
    {
        $loginFacebookURL = $laravelFacebookSDK->getLoginUrl(['email']);
        return view('user.setup', ['loginFacebookURL' => $loginFacebookURL]);
    }

    public function facebookCallback(LaravelFacebookSDK $laravelFacebookSDK)
    {
        // Obtain an access token.
        try {
            $token = $laravelFacebookSDK->getAccessTokenFromRedirect();
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }
        // Access token will be null if the user denied the request
        // or if someone just hit this URL outside of the OAuth flow.
        if (! $token) {
            // Get the redirect helper
            $helper = $laravelFacebookSDK->getRedirectLoginHelper();
            if (!$helper->getError()) {
                abort(403, 'Unauthorized action.');
            }
            // User denied the request
            dd(
                $helper->getError(),
                $helper->getErrorCode(),
                $helper->getErrorReason(),
                $helper->getErrorDescription()
            );
        }
        if (!$token->isLongLived()) {
            // OAuth 2.0 client handler
            $oauth_client = $laravelFacebookSDK->getOAuth2Client();
            // Extend the access token.
            try {
                $token = $oauth_client->getLongLivedAccessToken($token);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                dd($e->getMessage());
            }
        }
        $laravelFacebookSDK->setDefaultAccessToken($token);
        // Get basic info on the user from Facebook.
        try {
            $response = $laravelFacebookSDK->get('/me?fields=id,name');
            $facebookUser = $response->getGraphUser();
            $facebookUserAccessToken = (array) $token;
            if (!User::where('social_id', '=', $facebookUser['id'])->first()) {
                User::create([
                    'social_id' => $facebookUser["id"],
                    'social_name' => $facebookUser["name"],
                    'type' => 1,
                    'access_token' => array_values($facebookUserAccessToken)[0],
                    'expired_date' => array_values($facebookUserAccessToken)[1]
                ]);
            }
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }
        return redirect('facebook/fanpage');
    }
}
