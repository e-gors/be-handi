<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'user_id', 'profile_id', 'contract_id', 'day', 'start_time', 'end_time', 'date'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
