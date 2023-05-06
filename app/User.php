<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable, SoftDeletes;

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
}
