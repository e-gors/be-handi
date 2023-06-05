<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'uuid',
        'title',
        'description',
        'skills',
        'category',
        'position',
        'job_type',
        'days',
        'rate',
        'budget',
        'locations',
        'questions',
        'images',
        'post_url',
        'visibility'
    ];

    protected $casts = [
        'locations' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shortlistedByUsers()
    {
        return $this->hasMany(Shortlist::class, 'post_id');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }
}
