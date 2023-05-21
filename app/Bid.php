<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bid extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'post_id', 'proposal', 'rate', 'status', 'images'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
