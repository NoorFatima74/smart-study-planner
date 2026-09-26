<?php
namespace App\Services;

use App\Models\Task;
use Carbon\Carbon;

class RecommendationService
{
    private const PRIORITY_SCORES = [
        'high' => 40,
        'medium' => 25,
        'low' => 10,
    ];

    private const DIFFICULTY_SCORES = [
        'hard' => 8,
        'medium' => 5,
        'easy' => 2,
    ];

    public function getTopRecommendation(int $userId): ?array
    {
        $recommendations = $this->getRankedTasks($userId, 1);

        return $recommendations[0] ?? null;
    }

    public function getRankedTasks(int $userId, int $limit = 5): array
    {
        $tasks = Task::where('user_id', $userId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->with('subject')
            ->get();

        $scored = $tasks->map(function (Task $task) {
            [$score, $reasons] = $this->scoreTask($task);

            return [
                'task' => $task,
                'score' => $score,
                'reasons' => $reasons,
            ];
        });

        return $scored->sortByDesc('score')->take($limit)->values()->toArray();
    }

    private function scoreTask(Task $task): array
    {
        $score = 0;
        $reasons = [];

        // Deadline urgency — highest weight
        if ($task->deadline) {
            $daysUntil = Carbon::now()->diffInDays($task->deadline, false);

            if ($daysUntil < 0) {
                $score += 60;
                $reasons[] = 'Overdue by ' . abs((int) $daysUntil) . ' day(s)';
            } elseif ($daysUntil === 0) {
                $score += 55;
                $reasons[] = 'Due today';
            } elseif ($daysUntil <= 1) {
                $score += 45;
                $reasons[] = 'Due tomorrow';
            } elseif ($daysUntil <= 3) {
                $score += 30;
                $reasons[] = 'Deadline approaching (' . (int) $daysUntil . ' days)';
            } elseif ($daysUntil <= 7) {
                $score += 15;
                $reasons[] = 'Due within a week';
            }
        }

        // Priority — second-highest weight
        $priorityScore = self::PRIORITY_SCORES[$task->priority] ?? 0;
        $score += $priorityScore;
        if ($task->priority === 'high') {
            $reasons[] = 'High priority';
        }

        // Difficulty — minor tiebreaker (harder tasks nudged up)
        $score += self::DIFFICULTY_SCORES[$task->difficulty] ?? 0;

        // Remaining time — minor factor (shorter tasks nudged up slightly)
        if ($task->estimated_minutes) {
            if ($task->estimated_minutes <= 30) {
                $score += 6;
                $reasons[] = 'Quick win (' . $task->estimated_minutes . ' min)';
            } elseif ($task->estimated_minutes <= 60) {
                $score += 3;
            }
        }

        // In-progress tasks get a small nudge to encourage finishing what's started
        if ($task->status === 'in_progress') {
            $score += 5;
            $reasons[] = 'Already in progress';
        }

        if (empty($reasons)) {
            $reasons[] = ucfirst($task->priority) . ' priority task';
        }

        return [$score, $reasons];
    }
}