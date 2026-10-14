<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateAiPlanRequest;
use App\Models\AiStudyPlan;
use App\Models\Subject;
use App\Services\StudyPlannerAiService;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

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
        return redirect()->route('ai-planner.create')->withErrors(['ai' => 'No plan to save. Please generate one first.']);
    }

    $data = $pending['data'];
    $plan = $pending['plan'];
    $userId = auth()->id();
    $clean = fn ($s) => str_replace('**', '', (string) $s);

    DB::transaction(function () use ($data, $plan, $userId, $clean) {
        $subjectId = $data['subject_id']
            ?? Subject::firstOrCreate(['user_id' => $userId, 'name' => 'General'])->id;

        AiStudyPlan::create([
            'user_id'    => $userId,
            'subject_id' => $data['subject_id'] ?? null,
            'exam_name'  => $data['goal'],
            'exam_date'  => $data['deadline'] ?? null,
            'plan_data'  => $plan,
            'is_saved'   => true,
        ]);

        $base = [
            'user_id'    => $userId,
            'subject_id' => $subjectId,
            'priority'   => 'medium',
            'difficulty' => 'medium',
            'status'     => 'pending',
        ];

        if ($data['plan_type'] === 'single') {
            $items = collect($plan['plan'])->flatMap(fn ($d) => $d['items'])
                ->where('activity', '!=', 'Break');

            Task::create($base + [
                'title'             => $data['goal'],
                'description'       => $clean($items->pluck('title')->implode(' → ')),
                'estimated_minutes' => $items->sum('duration'),
                'deadline'          => now()->toDateString(),
            ]);
        } else {
            foreach ($plan['plan'] as $day) {
                foreach ($day['items'] as $item) {
                    if ($item['activity'] === 'Break') {
                        continue;
                    }

                    Task::create($base + [
                        'title'             => $clean($item['title']),
                        'description'       => $clean(implode(' • ', $item['steps'])),
                        'estimated_minutes' => $item['duration'],
                        'deadline'          => $day['date'],
                    ]);
                }
            }
        }
    });

    session()->forget('pending_ai_plan');

    return redirect()->route('dashboard')->with('success', 'Study plan added to your planner!');
}
}