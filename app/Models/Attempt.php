<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attempt extends Model
{
    protected $fillable = [
        "user_id",
        "question_id",
        "answer_text",
        "estimated_band",
        "strengths",
        "areas_to_improve"
    ];

    protected $casts = [
        "estimated_band" => "float",
        "strengths" => "array",
        "areas_to_improve" => "array"
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
