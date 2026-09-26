<?php
namespace App\Services;

use App\Models\StudySession;
use App\Models\Task;
use Carbon\Carbon;

class AnalyticsService
{
    public function studyMinutesByDay(int $userId, int $days = 7): array
    {
        $start = Carbon::today()->subDays($days - 1);

        $sessions = StudySession::where('user_id', $userId)
            ->whereNotNull('duration_minutes')
            ->whereDate('started_at', '>=', $start)
            ->selectRaw('DATE(started_at) as day, SUM(duration_minutes) as minutes')
            ->groupBy('day')
            ->pluck('minutes', 'day');

        $labels = [];
        $data = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $key = $date->toDateString();

            $labels[] = $date->format('D');
            $data[] = (int) ($sessions[$key] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function studyMinutesBySubject(int $userId): array
    {
        $rows = StudySession::where('study_sessions.user_id', $userId)
            ->whereNotNull('duration_minutes')
            ->join('tasks', 'tasks.id', '=', 'study_sessions.task_id')
            ->join('subjects', 'subjects.id', '=', 'tasks.subject_id')
            ->selectRaw('subjects.name as subject, SUM(study_sessions.duration_minutes) as minutes')
            ->groupBy('subjects.id', 'subjects.name')
            ->orderByDesc('minutes')
            ->get();

        return [
            'labels' => $rows->pluck('subject')->toArray(),
            'data' => $rows->pluck('minutes')->map(fn ($m) => (int) $m)->toArray(),
        ];
    }

    public function taskCompletionBreakdown(int $userId): array
    {
        $completed = Task::where('user_id', $userId)->where('status', 'completed')->count();
        $inProgress = Task::where('user_id', $userId)->where('status', 'in_progress')->count();
        $pending = Task::where('user_id', $userId)->where('status', 'pending')->count();

        return [
            'labels' => ['Completed', 'In Progress', 'Pending'],
            'data' => [$completed, $inProgress, $pending],
        ];
    }

    public function summaryStats(int $userId): array
    {
        $totalMinutes = StudySession::where('user_id', $userId)->whereNotNull('duration_minutes')->sum('duration_minutes');
        $totalTasks = Task::where('user_id', $userId)->count();
        $completedTasks = Task::where('user_id', $userId)->where('status', 'completed')->count();

        $mostStudiedSubject = $this->studyMinutesBySubject($userId);
        $topSubject = $mostStudiedSubject['labels'][0] ?? '—';

        return [
            'total_hours' => round($totalMinutes / 60, 1),
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
            'top_subject' => $topSubject,
        ];
    }
}