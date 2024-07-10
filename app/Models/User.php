<?php

namespace App\Models;

use App\ShortList;
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

    public function shortlists()
    {
        return $this->hasMany(ShortList::class);
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
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

    public function tracker()
    {
        return $this->hasOne(Tracker::class);
    }

    public function workExperience()
    {
        return $this->hasOne(WorkExperience::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Deleting event listener to delete associated data
        static::deleting(function ($user) {
            $user->profile()->delete();
            $user->skills()->detach();
            $user->categories()->detach();
            $user->posts()->delete();
            $user->shortlists()->delete();
            $user->bids()->delete();
            $user->offers()->delete();
            $user->ratings()->delete();
            $user->projects()->delete();
            $user->tracker()->delete();
            $user->workExperience()->delete();
            $user->deleteRelatedFiles();
            $user->deleteRelatedRecords();
        });
    }
}
