<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'position',
        'start_date',
        'end_date',
        'notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
