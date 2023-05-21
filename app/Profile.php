<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'background',
        'first_name',
        'last_name',
        'gender',
        'address',
        'profile_url',
        'background_url',
        'rate',
        'availability',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'profile_link'
    ];

    protected $appends = [
        'full_name',
        'profile_completeness'
    ];

    public function getFullNameAttribute()
    {
        return implode(' ', [$this->first_name, $this->last_name]);
    }

    public function getProfileCompletenessAttribute()
    {
        $weights = [
            'first_name' => 1,
            'last_name' => 1,
            'background' => 1,
            'profile_url' => 1,
            'background_url' => 1,
            'address' => 1,
            'rate' => 1,
            'facebook_url' => 1,
            'instagram_url' => 0.5,
            'twitter_url' => 0.5,
            'gender' => 1
        ];

        $completedFields = 0;

        if ($this->first_name) {
            $completedFields += $weights['first_name'];
        }
        if ($this->last_name) {
            $completedFields += $weights['last_name'];
        }
        if ($this->background) {
            $completedFields += $weights['background'];
        }
        if ($this->profile_url) {
            $completedFields += $weights['profile_url'];
        }
        if ($this->background_url) {
            $completedFields += $weights['background_url'];
        }
        if ($this->address) {
            $completedFields += $weights['address'];
        }
        if ($this->contact_number) {
            $completedFields += $weights['contact_number'];
        }
        if ($this->daily_rate) {
            $completedFields += $weights['daily_rate'];
        }
        if ($this->facebook_url) {
            $completedFields += $weights['facebook_url'];
        }
        if ($this->instagram_url) {
            $completedFields += $weights['instagram_url'];
        }
        if ($this->twitter_url) {
            $completedFields += $weights['twitter_url'];
        }


        return $completedFields;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shortlistedByUsers()
    {
        return $this->belongsToMany(User::class, 'shortlists', 'profile_id', 'user_id')
            ->where('favorite_type', 'profile');
    }
}
