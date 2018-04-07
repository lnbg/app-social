<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookFan extends Model
{
    protected $table = 'facebook_fans';
    protected $guarded = [];

    public function facebookAnalytics()
    {
        return $this->belongsTo('App\Models\FacebookAnalytics', 'facebook_analytics_id');
    }
}
