<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'user_id', 'profile_id', 'rate_type', 'rating'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
