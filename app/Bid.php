<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bid extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'post_id', 'description', 'amount', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
