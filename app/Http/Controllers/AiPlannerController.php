<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateAiPlanRequest;
use App\Models\AiStudyPlan;
use App\Models\Subject;
use App\Services\StudyPlannerAiService;
use Illuminate\Http\Request;
use App\Models\Task;

class AiPlannerController extends Controller
{
    public function __construct(protected StudyPlannerAiService $planner) {}

    public function create()
    {
        $subjects = Subject::where('user_id', auth()->id())->get();
        return view('ai-planner.create', compact('subjects'));
    }

    public function generate(GenerateAiPlanRequest $request)
{
    $data = $request->validated();

    try {
        $plan = $this->planner->generatePlan($data, auth()->id());
    } catch (\Exception $e) {
        return back()->withErrors(['ai' => $e->getMessage()])->withInput();
    }

    session(['pending_ai_plan' => ['data' => $data, 'plan' => $plan]]);

    return redirect()->route('ai-planner.review');
}

public function review()
{
    $pending = session('pending_ai_plan');

    if (!$pending) {
        return redirect()->route('ai-planner.create')->withErrors(['ai' => 'No plan to show — please generate one first.']);
    }

    return view('ai-planner.review', ['data' => $pending['data'], 'plan' => $pending['plan']]);
}

    public function save(Request $request)
{
    $pending = session('pending_ai_plan');

    if (!$pending) {
        return redirect()->route('ai-planner.create')->withErrors(['ai' => 'No plan to save — please generate one first.']);
    }

    AiStudyPlan::create([
        'user_id' => auth()->id(),
        'subject_id' => $pending['data']['subject_id'],
        'exam_name' => $pending['data']['exam_name'],
        'exam_date' => $pending['data']['exam_date'],
        'plan_data' => $pending['plan'],
        'is_saved' => true,
    ]);

    foreach ($pending['plan']['plan'] as $day) {
        foreach ($day['items'] as $item) {
            Task::create([
                'user_id' => auth()->id(),
                'subject_id' => $pending['data']['subject_id'],
                'title' => $item['title'],
                'description' => $item['reason'] ?? null,
                'priority' => 'medium',
                'difficulty' => 'medium',
                'estimated_minutes' => $item['duration'],
                'deadline' => $day['date'],
                'status' => 'pending',
            ]);
        }
    }

    session()->forget('pending_ai_plan');

    return redirect()->route('dashboard')->with('success', 'Study plan added to your planner!');
}
}