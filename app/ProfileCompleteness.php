<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfileCompleteness extends Model
{
    protected $fillable = [
        'section', 'completeness', 'status', 'next_steps'
    ];
}
