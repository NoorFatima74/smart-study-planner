<?php

namespace App\Services;

use Exception;

class QuizAiService
{
    protected const COUNT = 10;

    public function __construct(protected AiService $aiService) {}

    public function generate(array $data): array
    {
        $prompt = $this->buildPrompt($data);
        $lastError = null;

        // Retry once if the AI returns a malformed quiz
        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $raw = $this->aiService->generate($prompt);

            try {
                return $this->parse($raw);
            } catch (Exception $e) {
                $lastError = $e;
            }
        }

        throw $lastError;
    }

    protected function buildPrompt(array $data): string
    {
        $topic = !empty($data['topic'])
            ? $data['topic']
            : 'Cover a balanced mix of the subject\'s core topics.';

        $count = self::COUNT;

        return <<<PROMPT
You are a quiz generator. Create a multiple-choice quiz.

Subject: {$data['subject']}
Topic focus: {$topic}
Level: {$data['level']}

Level guide:
- beginner: fundamentals, definitions, basic recall
- intermediate: understanding and applying concepts
- advanced: deeper analysis, edge cases, tricky distinctions

Rules:
- Exactly {$count} questions.
- Each question has exactly 4 options and exactly ONE correct option.
- Questions must be factually accurate and unambiguous.
- Do not use "all of the above" or "none of the above".
- Vary which option position is correct.
- Keep questions under 25 words, options under 12 words.
- "explanation": one short sentence (max 25 words) saying why the answer is correct.
- "correct" is the 0-based index of the correct option (0 to 3).

Return STRICT JSON only, no extra text, in exactly this shape:

{
  "questions": [
    {
      "question": "string",
      "options": ["string", "string", "string", "string"],
      "correct": 0,
      "explanation": "string"
    }
  ]
}
PROMPT;
    }

    protected function parse(string $raw): array
    {
        $json = json_decode($raw, true);
        $items = $json['questions'] ?? null;

        if (!is_array($items) || count($items) < self::COUNT) {
            throw new Exception('AI returned an incomplete quiz. Please try again.');
        }

        $questions = [];

        foreach (array_slice($items, 0, self::COUNT) as $item) {
            $options = array_values(array_map('strval', (array) ($item['options'] ?? [])));
            $correct = $item['correct'] ?? null;

            if (
                empty($item['question'])
                || count($options) !== 4
                || count(array_unique($options)) !== 4
                || !is_numeric($correct)
                || (int) $correct < 0
                || (int) $correct > 3
            ) {
                throw new Exception('AI returned an invalid question. Please try again.');
            }

            // Shuffle so the correct answer isn't biased toward one position
            $correctText = $options[(int) $correct];
            shuffle($options);

            $questions[] = [
                'question'    => (string) $item['question'],
                'options'     => $options,
                'correct'     => array_search($correctText, $options, true),
                'explanation' => (string) ($item['explanation'] ?? ''),
            ];
        }

        return $questions;
    }
}