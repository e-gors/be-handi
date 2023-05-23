<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'post_id', 'bid_id', 'offer_id', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
