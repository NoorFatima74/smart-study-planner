<?php

namespace App\Services;

use App\Models\StudySession;
use Carbon\Carbon;
use App\Services\GamificationService;
use App\Models\User;

class StreakService
{
    
    private function studyDates(int $userId): array
    {
        return StudySession::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereNotNull('ended_at')
            ->selectRaw('DISTINCT DATE(started_at) as study_date')
            ->orderByDesc('study_date')
            ->pluck('study_date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->toArray();
    }

     public function currentStreak(int $userId): int
{
    $dates = $this->studyDates($userId);

    if (empty($dates)) {
        return 0;
    }

    $today = Carbon::today();
    $yesterday = Carbon::yesterday()->toDateString();

    if ($dates[0] !== $today->toDateString() && $dates[0] !== $yesterday) {
        return 0;
    }

    $streak = 0;
    $expected = Carbon::parse($dates[0]);

    foreach ($dates as $date) {
        if ($date === $expected->toDateString()) {
            $streak++;
            $expected = $expected->subDay();
        } else {
            break;
        }
    }

    $user = \App\Models\User::find($userId);

    if ($user) {
        app(GamificationService::class)->checkStreakAchievement($user, $streak);
    }

    return $streak;
}

    public function longestStreak(int $userId): int
    {
        $dates = array_reverse($this->studyDates($userId)); // oldest first

        if (empty($dates)) {
            return 0;
        }

        $longest = 1;
        $current = 1;

        for ($i = 1; $i < count($dates); $i++) {
            $prev = Carbon::parse($dates[$i - 1]);
            $curr = Carbon::parse($dates[$i]);

            if ($prev->diffInDays($curr) === 1) {
                $current++;
                $longest = max($longest, $current);
            } else {
                $current = 1;
            }
        }

        return $longest;
    }
}