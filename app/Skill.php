<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Skill extends Model
{

    use NodeTrait, HasFactory;

    protected $fillable = [
        'name',
        'parent_id'
    ];


    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Skill::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Skill::class, 'parent_id');
    }
}
