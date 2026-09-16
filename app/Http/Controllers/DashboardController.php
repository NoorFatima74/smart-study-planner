<?php

namespace App\Http\Controllers;

use App\Models\StudySession;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $todayTasks = Task::where('user_id', $userId)
            ->whereDate('deadline', now()->toDateString())
            ->orderByRaw("FIELD(priority, 'high','medium','low')")
            ->get();

        $completedToday = $todayTasks->where('status', 'completed')->count();
        $totalToday = $todayTasks->count();

        $studyMinutesToday = StudySession::where('user_id', $userId)
            ->whereNotNull('duration_minutes')
            ->whereDate('started_at', now()->toDateString())
            ->sum('duration_minutes');

        $upcomingDeadlines = Task::where('user_id', $userId)
            ->where('status', '!=', 'completed')
            ->whereNotNull('deadline')
            ->where('deadline', '>=', now())
            ->orderBy('deadline')
            ->take(5)
            ->get();

        // Simple placeholder pick until Unit 10's real scoring engine:
        // highest priority, then soonest deadline, among incomplete tasks.
        $recommendedTask = Task::where('user_id', $userId)
            ->where('status', '!=', 'completed')
            ->orderByRaw("FIELD(priority, 'high','medium','low')")
            ->orderByRaw('deadline IS NULL, deadline ASC')
            ->first();

        return view('dashboard.index', compact(
            'todayTasks',
            'completedToday',
            'totalToday',
            'studyMinutesToday',
            'upcomingDeadlines',
            'recommendedTask'
        ));
    }
}