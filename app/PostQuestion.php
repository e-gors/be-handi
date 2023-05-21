<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostQuestion extends Model
{
    protected $fillable = [
        'post_id', 'status', 'question'
    ];
}
