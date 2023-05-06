<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    
    protected $fillable = ['user_id', 'is_online', 'last_online_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
