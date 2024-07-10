<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialNetwork extends Model
{
    protected $fillable = [
        'user_id', 'facebook_url', 'instagram_url', 'twitter_url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
