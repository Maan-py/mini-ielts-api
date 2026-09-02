<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = ["part", "topic", "prompt"];
    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }
}
