<?php
namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function index(AnalyticsService $analyticsService)
    {
        $userId = Auth::id();

        return view('analytics.index', [
            'dailyMinutes' => $analyticsService->studyMinutesByDay($userId),
            'subjectMinutes' => $analyticsService->studyMinutesBySubject($userId),
            'completionBreakdown' => $analyticsService->taskCompletionBreakdown($userId),
            'summary' => $analyticsService->summaryStats($userId),
        ]);
    }
}