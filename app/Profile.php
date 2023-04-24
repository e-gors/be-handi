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
        'progress'
    ];

    protected $appends = [
        'full_name',
    ];

    public function getFullNameAttribute()
    {
        return implode(' ', [$this->first_name, $this->last_name]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
