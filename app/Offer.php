<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'user_id',
        'profile_id',
        'post_id',
        'type',
        'days',
        'rate',
        'budget',
        'instructions',
        'images',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function worker()
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'user_id');
    }
}
