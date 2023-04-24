<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{

    protected $fillable = [
        'user_id',
        'name',
    ];

    protected $casts = [
        'name' => 'array'
    ];
    
    public function getArrayColumnAttribute($value)
    {
        return unserialize($value);
    }

    public function setArrayColumnAttribute($value)
    {
        $this->attributes['name'] = serialize($value);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
