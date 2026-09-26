<?php
namespace App\Services;

use App\Models\Task;
use Carbon\Carbon;

class ReschedulingService
{
    public function __construct(private StudyPlannerService $plannerService)
    {
    }

    public function detectMissed(int $userId): array
    {
        return Task::where('user_id', $userId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('deadline')
            ->where('deadline', '<', Carbon::now())
            ->with('subject')
            ->orderBy('deadline')
            ->get()
            ->toArray();
    }

    public function rescheduleMissed(int $userId, int $availableMinutesPerDay, int $extensionDays = 3): array
    {
        $missed = Task::where('user_id', $userId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('deadline')
            ->where('deadline', '<', Carbon::now())
            ->whereNotNull('estimated_minutes')
            ->get();

        if ($missed->isEmpty()) {
            return ['rescheduled' => [], 'plan' => $this->plannerService->generateSchedule($userId, $availableMinutesPerDay)];
        }

        foreach ($missed as $task) {
            $newDeadline = Carbon::now()->addDays($extensionDays)->endOfDay();

            $task->update([
                'original_deadline' => $task->original_deadline ?? $task->deadline, // only set on first reschedule
                'deadline' => $newDeadline,
                'rescheduled_at' => Carbon::now(),
                'reschedule_count' => $task->reschedule_count + 1,
            ]);
        }

        $plan = $this->plannerService->generateSchedule($userId, $availableMinutesPerDay);

        return [
            'rescheduled' => $missed->fresh(['subject'])->toArray(),
            'plan' => $plan,
        ];
    }
}