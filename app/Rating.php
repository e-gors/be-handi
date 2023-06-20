<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rating extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'post_id',
        'worker_id',
        'comment',
        'rating'
    ];

    public function reviewable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
