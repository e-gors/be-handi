<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'user_id', 'profile_id', 'schedule_id', 'title', 'description', 'amount', 'status'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
