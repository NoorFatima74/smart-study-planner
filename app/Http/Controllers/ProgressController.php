<?php

namespace App\Http\Controllers;

use App\Models\StudySession;
use App\Models\Task;
use App\Services\StreakService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    public function __construct(private StreakService $streakService)
    {
    }

    public function index()
    {
        $userId = Auth::id();

        $tasksCompleted = Task::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();

        $totalTasks = Task::where('user_id', $userId)->count();

        $completionRate = $totalTasks > 0
            ? round(($tasksCompleted / $totalTasks) * 100)
            : 0;

        $totalStudyMinutes = StudySession::where('user_id', $userId)
            ->whereNotNull('duration_minutes')
            ->sum('duration_minutes');

        $mostStudiedSubject = DB::table('study_sessions')
            ->join('tasks', 'tasks.id', '=', 'study_sessions.task_id')
            ->join('subjects', 'subjects.id', '=', 'tasks.subject_id')
            ->where('study_sessions.user_id', $userId)
            ->whereNotNull('study_sessions.duration_minutes')
            ->select('subjects.name', DB::raw('SUM(study_sessions.duration_minutes) as total_minutes'))
            ->groupBy('subjects.id', 'subjects.name')
            ->orderByDesc('total_minutes')
            ->first();

        $currentStreak = $this->streakService->currentStreak($userId);
        $longestStreak = $this->streakService->longestStreak($userId);

        return view('progress.index', compact(
            'tasksCompleted',
            'totalTasks',
            'completionRate',
            'totalStudyMinutes',
            'mostStudiedSubject',
            'currentStreak',
            'longestStreak'
        ));
    }
}