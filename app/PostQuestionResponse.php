<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostQuestionResponse extends Model
{
    protected $fillable = [
        'post_question_id', 'response'
    ];
}
