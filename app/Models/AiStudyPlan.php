<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiStudyPlan extends Model
{
    protected $fillable = ['user_id', 'subject_id', 'exam_name', 'exam_date', 'plan_data', 'is_saved'];

    protected $casts = [
        'plan_data' => 'array',
        'exam_date' => 'date',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}