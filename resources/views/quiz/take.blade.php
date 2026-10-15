@extends('layouts.app')

@section('title', 'AI Quiz')
@section('page-title', $quiz->subject . ' Quiz')
@section('page-subtitle', ucfirst($quiz->level) . ($quiz->topic ? ' · ' . $quiz->topic : '') . ' · ' . count($quiz->questions) . ' questions')

@section('content')
    @if ($errors->any())
        <div class="card" style="border-color:#e74c3c; margin-bottom:16px; color:#e74c3c; font-size:13px; padding:12px;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('quiz.submit', $quiz) }}" id="quiz-form">
        @csrf

        <div class="quiz-progress"><span id="answered">0</span> / {{ count($quiz->questions) }} answered</div>

        @foreach ($quiz->questions as $i => $q)
            <div class="card quiz-q">
                <div class="quiz-q-head"><span class="quiz-num">Q{{ $i + 1 }}</span>{{ $q['question'] }}</div>
                <div class="quiz-options">
                    @foreach ($q['options'] as $j => $option)
                        <label class="quiz-option">
                            <input type="radio" name="answers[{{ $i }}]" value="{{ $j }}" required>
                            <span class="quiz-letter">{{ chr(65 + $j) }}</span>
                            <span>{{ $option }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn">✅ Submit Quiz</button>
    </form>
@endsection

@section('scripts')
<script>
    const form = document.getElementById('quiz-form');
    const counter = document.getElementById('answered');
    form.addEventListener('change', () => {
        const names = new Set(
            [...form.querySelectorAll('input[type=radio]:checked')].map(r => r.name)
        );
        counter.textContent = names.size;
    });
</script>
@endsection