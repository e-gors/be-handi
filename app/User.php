<?php

namespace App;

use Illuminate\Support\Carbon;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable, SoftDeletes, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'email', 'username', 'first_name', 'last_name', 'role', 'contact_number',  'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'full_name',
    ];

    protected $dates = [
        'email_verified_at',
    ];


    public function getFullNameAttribute()
    {
        return implode(' ', [$this->first_name, $this->last_name]);
    }


    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    public function socialNetwork()
    {
        return $this->hasOne(SocialNetwork::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class)->with('children');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->with('children');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function shortlist()
    {
        return $this->hasMany(Shortlist::class);
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
