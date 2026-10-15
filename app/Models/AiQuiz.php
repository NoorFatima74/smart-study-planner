<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiQuiz extends Model
{
    protected $fillable = [
        'user_id', 'subject', 'topic', 'level',
        'questions', 'answers', 'score', 'completed_at',
    ];

    protected $casts = [
        'questions'    => 'array',
        'answers'      => 'array',
        'completed_at' => 'datetime',
    ];
}