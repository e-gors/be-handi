<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShortList extends Model
{
    protected $table = 'shortlists';

    protected $fillable = [
        "user_id", "profile_id", 'post_id', 'favorite_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
