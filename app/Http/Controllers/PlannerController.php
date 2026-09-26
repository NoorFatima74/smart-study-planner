<?php
namespace App\Http\Controllers;

use App\Services\StudyPlannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ReschedulingService;


class PlannerController extends Controller
{
    public function index(Request $request, StudyPlannerService $plannerService)
    {
        $minutesPerDay = (int) $request->get('minutes_per_day', 120);

        $plan = $plannerService->generateSchedule(Auth::id(), $minutesPerDay);

        return view('planner.index', [
            'plan' => $plan,
            'minutesPerDay' => $minutesPerDay,
        ]);
    }

    public function missed(ReschedulingService $reschedulingService){
    $missed = $reschedulingService->detectMissed(Auth::id());

    return view('planner.missed', [
        'missed' => $missed,
    ]);
}

public function reschedule(Request $request, ReschedulingService $reschedulingService){
    $minutesPerDay = (int) $request->get('minutes_per_day', 120);

    $result = $reschedulingService->rescheduleMissed(Auth::id(), $minutesPerDay);

    return redirect()->route('planner.index', ['minutes_per_day' => $minutesPerDay])
        ->with('status', count($result['rescheduled']) . ' task(s) rescheduled.');
   }
}