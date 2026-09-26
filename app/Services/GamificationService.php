<?php
namespace App\Services;

use App\Models\User;
use App\Models\Task;
use App\Models\Achievement;

class GamificationService
{
    private const XP_MAP = [
        'low' => 10,
        'medium' => 15,
        'high' => 25,
    ];

    public function awardTaskCompletionXp(User $user, Task $task): array
    {
        $xpGained = self::XP_MAP[$task->priority] ?? 10;

        $user->xp += $xpGained;
        $leveledUp = $this->checkLevelUp($user);
        $user->save();

        $newAchievements = $this->checkTaskAchievements($user);

        return [
            'xp_gained' => $xpGained,
            'leveled_up' => $leveledUp,
            'new_level' => $user->level,
            'new_achievements' => $newAchievements,
        ];
    }

    private function checkLevelUp(User $user): bool
    {
        $leveledUp = false;

        while ($user->xp >= $user->level * 100) {
            $user->level += 1;
            $leveledUp = true;
            $this->unlockAchievement($user, 'level', $user->level);
        }

        return $leveledUp;
    }

    public function checkStreakAchievement(User $user, int $currentStreak): ?Achievement
    {
        return $this->unlockAchievement($user, 'streak', $currentStreak);
    }

    private function checkTaskAchievements(User $user): array
    {
        $completedCount = $user->tasks()->where('status', 'completed')->count();
        $unlocked = [];

        foreach ([1, 10, 50, 100] as $threshold) {
            if ($completedCount >= $threshold) {
                $achievement = $this->unlockAchievement($user, 'tasks_completed', $threshold);
                if ($achievement) {
                    $unlocked[] = $achievement;
                }
            }
        }

        return $unlocked;
    }

    private function unlockAchievement(User $user, string $type, int $threshold): ?Achievement
    {
        $achievement = Achievement::where('type', $type)
            ->where('threshold', $threshold)
            ->first();

        if (!$achievement) {
            return null;
        }

        $alreadyEarned = $user->achievements()
            ->where('achievement_id', $achievement->id)
            ->exists();

        if ($alreadyEarned) {
            return null;
        }

        $user->achievements()->attach($achievement->id, [
            'earned_at' => now(),
        ]);

        return $achievement;
    }
}