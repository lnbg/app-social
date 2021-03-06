<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookAnalytics extends Model
{
    protected $table = 'facebook_analytics';
    protected $guarded = [];

    public function fans() {
        return $this->hasMany('App\Models\FacebookFan');
    }
}
