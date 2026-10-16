@extends('layouts.app')

@php
    $total = count($quiz->questions);
    $pct = $total ? round($quiz->score / $total * 100) : 0;
    $msg = $pct >= 80 ? 'Excellent work!' : ($pct >= 50 ? 'Good effort. Review the ones you missed.' : 'Keep practicing, you will get there.');
@endphp

@section('title', 'Quiz Result')
@section('page-title', 'Quiz Result')
@section('page-subtitle', $quiz->subject . ' · ' . ucfirst($quiz->level) . ' · ' . $quiz->completed_at->format('d M Y'))

@section('content')
    <div class="card quiz-score">
        <div class="quiz-score-num">{{ $quiz->score }}/{{ $total }}</div>
        <div class="quiz-score-pct">{{ $pct }}% correct</div>
        <div class="quiz-score-msg">{{ $msg }}</div>
        <x-back-link :href="route('quiz.create')" label="Back to AI Quiz" />
    </div>

    @foreach ($quiz->questions as $i => $q)
        @php
            $mine = isset($quiz->answers[$i]) ? (int) $quiz->answers[$i] : null;
            $right = (int) $q['correct'];
        @endphp

        <div class="card quiz-q">
            <div class="quiz-q-head"><span class="quiz-num">Q{{ $i + 1 }}</span>{{ $q['question'] }}</div>
            <div class="quiz-options">
                @foreach ($q['options'] as $j => $option)
                    @php
                        $isCorrect = $j === $right;
                        $isMine = $j === $mine;
                        $cls = $isCorrect ? 'is-correct' : ($isMine ? 'is-wrong' : '');
                    @endphp
                    <div class="quiz-option {{ $cls }}">
                        <span class="quiz-letter">{{ chr(65 + $j) }}</span>
                        <span>{{ $option }}</span>
                        @if ($isCorrect)
                            <span class="quiz-tag ok">{{ $isMine ? '✓ Your answer' : 'Correct answer' }}</span>
                        @elseif ($isMine)
                            <span class="quiz-tag bad">Your answer</span>
                        @endif
                    </div>
                @endforeach
            </div>
            @if (!empty($q['explanation']))
                <div class="quiz-explain"><b>Why:</b> {{ $q['explanation'] }}</div>
            @endif
        </div>
    @endforeach

    <div style="display:flex; gap:12px; margin-top:20px; flex-wrap:wrap;">
        <form method="POST" action="{{ route('quiz.generate') }}">
            @csrf
            <input type="hidden" name="subject" value="{{ $quiz->subject }}">
            <input type="hidden" name="topic" value="{{ $quiz->topic }}">
            <input type="hidden" name="level" value="{{ $quiz->level }}">
            <button type="submit" class="btn">🔄 Try again (new questions)</button>
        </form>

        <a href="{{ route('quiz.create') }}" class="btn btn-secondary" style="text-decoration:none;">✨ New quiz</a>
    </div>
@endsection