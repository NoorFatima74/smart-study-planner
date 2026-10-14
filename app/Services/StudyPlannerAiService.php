<?php

namespace App\Services;

use App\Models\Task;
use Carbon\Carbon;
use Exception;

class StudyPlannerAiService
{
    protected const ACTIVITIES = ['Setup', 'Theory', 'Practice', 'Review', 'Break'];

    public function __construct(protected AiService $aiService) {}

    public function generatePlan(array $data, int $userId): array
    {
        $existingTasks = collect();

        if (!empty($data['subject_id'])) {
            $existingTasks = Task::where('user_id', $userId)
                ->where('subject_id', $data['subject_id'])
                ->whereIn('status', ['pending', 'in_progress'])
                ->get(['title', 'priority', 'difficulty', 'estimated_minutes', 'deadline']);
        }

        $raw = $this->aiService->generate($this->buildPrompt($data, $existingTasks));
        $plan = json_decode($raw, true);

        if (!$plan || !isset($plan['plan']) || !is_array($plan['plan']) || empty($plan['plan'])) {
            throw new Exception('AI returned an invalid plan format. Please try again.');
        }

        $plan = $this->normalizePlan($plan);
        $this->validatePlan($plan, $data);

        return $plan;
    }

    protected function buildPrompt(array $data, $existingTasks): string
    {
        $today = Carbon::today()->format('Y-m-d');
        $topics = !empty($data['topics']) ? $data['topics'] : 'Not specified. Choose sensible focus areas.';

        if ($data['plan_type'] === 'single') {
            $schedule = "Create ONE session for today ({$today}) using exactly one entry in \"plan\" with date {$today}.\n"
                . "Total of all item durations must not exceed {$data['session_minutes']} minutes.\n"
                . "Use short sequential items (a quick setup, the core work, a short review). Add a Break only if the session is longer than 45 minutes.";
        } else {
            $schedule = "Create a day-by-day plan between {$today} (inclusive) and {$data['deadline']} (inclusive).\n"
                . "Do not schedule more than {$data['daily_minutes']} minutes total per day.\n"
                . "Only use as many days as the goal actually needs.";
        }

        $tasksBlock = $existingTasks->isEmpty()
            ? ''
            : "\nStudent's existing tasks for this subject (prioritize urgent ones):\n"
              . $existingTasks->map(fn ($t) =>
                    "- {$t->title} (priority: {$t->priority}, ~{$t->estimated_minutes} min, deadline: {$t->deadline})"
                )->implode("\n") . "\n";

        return <<<PROMPT
You are a study planning assistant. Create a practical plan for the goal below.

Today's date: {$today}
Goal: {$data['goal']}
Topics / details: {$topics}
Student's current knowledge level: {$data['knowledge_level']}
{$tasksBlock}
Scheduling rules:
{$schedule}

Style rules (important):
- Keep everything SHORT. No long paragraphs.
- "steps": 2 to 3 bullet strings, max 12 words each.
- "why": one line, max 15 words.
- Wrap 1 or 2 key terms per item in **double asterisks** so they can be highlighted.
- "activity" must be exactly one of: Setup, Theory, Practice, Review, Break.
- "duration" is a whole number of minutes.

Return STRICT JSON only, no extra text, in exactly this shape:

{
  "plan": [
    {
      "date": "YYYY-MM-DD",
      "items": [
        {
          "title": "string",
          "activity": "Theory",
          "duration": 25,
          "steps": ["string", "string"],
          "why": "string"
        }
      ]
    }
  ]
}
PROMPT;
    }

    protected function normalizePlan(array $plan): array
    {
        foreach ($plan['plan'] as &$day) {
            $day['items'] = array_values($day['items'] ?? []);

            foreach ($day['items'] as &$item) {
                $activity = ucfirst(strtolower((string) ($item['activity'] ?? '')));
                $item['activity'] = in_array($activity, self::ACTIVITIES, true) ? $activity : 'Practice';
                $item['title'] = (string) ($item['title'] ?? 'Study step');
                $item['steps'] = array_values(array_filter(array_slice((array) ($item['steps'] ?? []), 0, 4)));
                $item['why'] = (string) ($item['why'] ?? $item['reason'] ?? '');
            }
            unset($item);
        }
        unset($day);

        return $plan;
    }

    protected function validatePlan(array $plan, array $data): void
    {
        $today = Carbon::today();
        $single = $data['plan_type'] === 'single';
        $lastDay = $single ? $today : Carbon::parse($data['deadline']);
        $limit = $single ? (int) $data['session_minutes'] : (int) $data['daily_minutes'];

        foreach ($plan['plan'] as $day) {
            if (empty($day['date']) || !strtotime($day['date'])) {
                throw new Exception('AI returned an invalid date.');
            }

            $date = Carbon::parse($day['date']);
            if ($date->lt($today) || $date->gt($lastDay)) {
                throw new Exception('AI scheduled a date outside the valid period. Please regenerate.');
            }

            if (empty($day['items'])) {
                throw new Exception('AI returned an empty day. Please regenerate.');
            }

            $total = 0;
            foreach ($day['items'] as $item) {
                if (!isset($item['duration']) || !is_numeric($item['duration']) || $item['duration'] <= 0) {
                    throw new Exception('AI returned an invalid duration.');
                }
                $total += $item['duration'];
            }

            if ($total > $limit) {
                throw new Exception('AI exceeded your available time. Please regenerate.');
            }
        }
    }
}