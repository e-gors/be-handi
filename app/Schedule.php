<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{

    protected $fillable = [
        'post_id',
        'offer_id',
        'start_date',
        'end_date'
    ];

    public function post()
    {
        return $this->belongsTo(User::class);
    }
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
