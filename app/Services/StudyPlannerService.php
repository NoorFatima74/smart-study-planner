<?php

namespace App\Services;

use App\Models\Task;
use Carbon\Carbon;

class StudyPlannerService
{
    public function generateSchedule(int $userId, int $availableMinutesPerDay): array
    {
        $tasks = Task::where('user_id', $userId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('deadline')
            ->whereNotNull('estimated_minutes')
            ->with('subject')
            ->get()
            ->sortBy([
                fn ($a, $b) => $a->deadline <=> $b->deadline,
                fn ($a, $b) => $this->priorityWeight($b->priority) <=> $this->priorityWeight($a->priority),
            ])
            ->values();

        if ($tasks->isEmpty()) {
            return ['days' => [], 'unscheduled' => []];
        }

        $remaining = $tasks->mapWithKeys(fn ($task) => [$task->id => $task->estimated_minutes])->toArray();

        $today = Carbon::today();
        $lastDeadline = $tasks->max(fn ($task) => $task->deadline->copy()->startOfDay());
        $horizon = $today->diffInDays($lastDeadline);

        $schedule = [];

        for ($i = 0; $i <= $horizon; $i++) {
            $day = $today->copy()->addDays($i);
            $capacity = $availableMinutesPerDay;
            $dayEntries = [];

            foreach ($tasks as $task) {
                if ($capacity <= 0) {
                    break;
                }

                if ($remaining[$task->id] <= 0) {
                    continue;
                }

                if ($day->gt($task->deadline->copy()->startOfDay())) {
                    continue; // past this task's deadline, skip
                }

                $allocate = min($remaining[$task->id], $capacity);

                if ($allocate > 0) {
                    $dayEntries[] = [
                        'task' => $task,
                        'minutes' => $allocate,
                    ];

                    $remaining[$task->id] -= $allocate;
                    $capacity -= $allocate;
                }
            }

            if (!empty($dayEntries)) {
                $schedule[] = [
                    'date' => $day,
                    'entries' => $dayEntries,
                ];
            }
        }

        $unscheduled = $tasks->filter(fn ($task) => $remaining[$task->id] > 0)
            ->map(fn ($task) => [
                'task' => $task,
                'minutes_short' => $remaining[$task->id],
            ])
            ->values()
            ->toArray();

        return ['days' => $schedule, 'unscheduled' => $unscheduled];
    }

    private function priorityWeight(string $priority): int
    {
        return match ($priority) {
            'high' => 3,
            'medium' => 2,
            'low' => 1,
            default => 0,
        };
    }
}