<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'target_minutes',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function minutesLogged(): int
    {
        return (int) StudySession::where('user_id', $this->user_id)
            ->whereNotNull('duration_minutes')
            ->whereDate('started_at', '>=', $this->start_date)
            ->whereDate('started_at', '<=', $this->end_date)
            ->sum('duration_minutes');
    }

    public function progressPercent(): int
    {
        if ($this->target_minutes <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->minutesLogged() / $this->target_minutes) * 100));
    }

    public function isActive(): bool
    {
        $today = now()->toDateString();

        return $this->start_date->toDateString() <= $today && $this->end_date->toDateString() >= $today;
    }
}