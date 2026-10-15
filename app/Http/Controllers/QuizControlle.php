<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateQuizRequest;
use App\Models\AiQuiz;
use App\Services\QuizAiService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct(protected QuizAiService $quizAi) {}

    public function create()
    {
        $recent = AiQuiz::where('user_id', auth()->id())
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->limit(5)
            ->get();

        return view('quiz.create', compact('recent'));
    }

    public function generate(GenerateQuizRequest $request)
    {
        $data = $request->validated();

        try {
            $questions = $this->quizAi->generate($data);
        } catch (\Exception $e) {
            return back()->withErrors(['ai' => $e->getMessage()])->withInput();
        }

        $quiz = AiQuiz::create([
            'user_id'   => auth()->id(),
            'subject'   => $data['subject'],
            'topic'     => $data['topic'] ?? null,
            'level'     => $data['level'],
            'questions' => $questions,
        ]);

        return redirect()->route('quiz.show', $quiz);
    }

    public function show(AiQuiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        if ($quiz->completed_at) {
            return redirect()->route('quiz.result', $quiz);
        }

        return view('quiz.take', compact('quiz'));
    }

    public function submit(Request $request, AiQuiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        if ($quiz->completed_at) {
            return redirect()->route('quiz.result', $quiz);
        }

        $request->validate([
            'answers'   => ['required', 'array'],
            'answers.*' => ['required', 'integer', 'between:0,3'],
        ]);

        $answers = $request->input('answers');
        $score = 0;
        $saved = [];

        foreach ($quiz->questions as $i => $q) {
            $given = isset($answers[$i]) ? (int) $answers[$i] : -1;
            $saved[$i] = $given;

            if ($given === (int) $q['correct']) {
                $score++;
            }
        }

        $quiz->update([
            'answers'      => $saved,
            'score'        => $score,
            'completed_at' => now(),
        ]);

        return redirect()->route('quiz.result', $quiz);
    }

    public function result(AiQuiz $quiz)
    {
        $this->authorizeQuiz($quiz);

        if (!$quiz->completed_at) {
            return redirect()->route('quiz.show', $quiz);
        }

        return view('quiz.result', compact('quiz'));
    }

    protected function authorizeQuiz(AiQuiz $quiz): void
    {
        abort_unless($quiz->user_id === auth()->id(), 403);
    }
}