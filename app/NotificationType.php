<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function emailNotification()
    {
        return $this->hasMany(EmailNotification::class);
    }
}
