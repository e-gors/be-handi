<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'post_id', 'bid_id', 'offer_id', 'status', 'start_date', 'end_date'
    ];

    protected $dates = [
        'start_date',
        'end_date',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function bid()
    {
        return $this->belongsTo(Bid::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
