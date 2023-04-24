<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'title', 'description', 'required_skills', 'salary_min', 'salary_max', 'location', 'is_published'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
