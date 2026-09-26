<?php

namespace App\Http\Controllers;

use App\Models\StudySession;
use App\Models\Task;
use App\Services\StreakService;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\Auth;
use App\Models\Goal;

class DashboardController extends Controller
{
    public function __construct(private StreakService $streakService)
    {
    }

    public function index(RecommendationService $recommendationService)
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

        $topRecommendation = $recommendationService->getTopRecommendation($userId);

        $currentStreak = $this->streakService->currentStreak($userId);
        $longestStreak = $this->streakService->longestStreak($userId);

        $activeGoal = Goal::where('user_id', $userId)
    ->whereDate('start_date', '<=', now())
    ->whereDate('end_date', '>=', now())
    ->orderByRaw("FIELD(type, 'daily','weekly','monthly')")
    ->first();

        return view('dashboard.index', compact(
            'todayTasks',
            'completedToday',
            'totalToday',
            'studyMinutesToday',
            'upcomingDeadlines',
            'topRecommendation',
            'currentStreak',
            'longestStreak',
            'activeGoal'
        ));
    }
}