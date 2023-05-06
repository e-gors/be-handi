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
        'contact_number',
        'profile_url',
        'background_url',
        'daily_rate',
        'availability'
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
            'background' => 2,
            'profile_url' => 2,
            'background_url' => 1,
            'address' => 1,
            'contact_number' => 1,
            'rate' => 1,
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
        if ($this->rate) {
            $completedFields += $weights['rate'];
        }


        return $completedFields;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
