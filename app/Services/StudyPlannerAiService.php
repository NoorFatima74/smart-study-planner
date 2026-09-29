<?php

namespace App\Services;

use App\Models\Subject;
use App\Models\Task;
use Carbon\Carbon;
use Exception;

class StudyPlannerAiService
{
    public function __construct(protected AiService $aiService) {}

    public function generatePlan(array $data, int $userId): array
    {
        $existingTasks = Task::where('user_id', $userId)
            ->where('subject_id', $data['subject_id'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->get(['title', 'priority', 'difficulty', 'estimated_minutes', 'deadline']);

        $prompt = $this->buildPrompt($data, $existingTasks);
        $raw = $this->aiService->generate($prompt);
        $plan = json_decode($raw, true);

        if (!$plan || !isset($plan['plan']) || !is_array($plan['plan'])) {
            throw new Exception('AI returned an invalid plan format. Please try again.');
        }

        $this->validatePlan($plan, $data);

        return $plan;
    }

    
    protected function buildPrompt(array $data, $existingTasks): string
{
    $today = Carbon::today()->format('Y-m-d');

    $tasksText = $existingTasks->isEmpty()
        ? 'No existing tasks for this subject yet.'
        : $existingTasks->map(fn ($t) =>
            "- {$t->title} (priority: {$t->priority}, difficulty: {$t->difficulty}, ~{$t->estimated_minutes} min, deadline: {$t->deadline})"
          )->implode("\n");

    return <<<PROMPT
You are a study planning assistant. Create a realistic day-by-day study plan.

Today's date: {$today}
Exam: {$data['exam_name']}
Exam date: {$data['exam_date']}
Available study time per day: {$data['daily_minutes']} minutes
Topics to cover: {$data['topics']}
Student's current knowledge level: {$data['knowledge_level']}

Student's existing tasks for this subject:
{$tasksText}

Rules:
- Only schedule dates between {$today} (inclusive) and {$data['exam_date']} (inclusive).
- Do not schedule more than {$data['daily_minutes']} minutes total per day.
- Prioritize existing high-priority tasks and approaching deadlines.
- Return STRICT JSON only, no explanation text, in exactly this shape:

{
  "plan": [
    {
      "date": "YYYY-MM-DD",
      "items": [
        { "title": "string", "duration": number, "reason": "string" }
      ]
    }
  ]
}
PROMPT;
}

    protected function validatePlan(array $plan, array $data): void
    {
        $examDate = Carbon::parse($data['exam_date']);
        $today = Carbon::today();

        foreach ($plan['plan'] as $day) {
            if (empty($day['date']) || !strtotime($day['date'])) {
                throw new Exception('AI returned an invalid date.');
            }

            $date = Carbon::parse($day['date']);
            if ($date->lt($today) || $date->gt($examDate)) {
                throw new Exception('AI scheduled a date outside the valid study period.');
            }

            $dailyTotal = 0;
            foreach ($day['items'] as $item) {
                if (!isset($item['duration']) || !is_numeric($item['duration']) || $item['duration'] <= 0) {
                    throw new Exception('AI returned an invalid duration.');
                }
                $dailyTotal += $item['duration'];
            }

            if ($dailyTotal > $data['daily_minutes']) {
                throw new Exception('AI exceeded the available daily study time.');
            }
        }
    }
}