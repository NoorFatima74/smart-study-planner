@extends('layouts.app')

@section('title', 'AI Quiz')
@section('page-title', 'AI Quiz')
@section('page-subtitle', 'Test yourself on any subject with 10 questions')

@section('content')
    @if ($errors->any())
        <div class="card" style="border-color:#e74c3c; margin-bottom:16px; color:#e74c3c; font-size:13px; padding:12px;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('quiz.generate') }}" class="ai-form" id="quiz-gen-form">
        @csrf

        <div class="field">
            <label>Subject</label>
            <input type="text" name="subject" value="{{ old('subject') }}" placeholder="e.g. Database Systems, World History, Python" required>
        </div>

        <div class="field">
            <label>Topic (optional)</label>
            <input type="text" name="topic" value="{{ old('topic') }}" placeholder="e.g. Normalization, World War II, List comprehensions">
        </div>

        <div class="field">
            <label>Level</label>
            <select name="level" required>
                @foreach (['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'] as $value => $label)
                    <option value="{{ $value }}" {{ old('level', 'beginner') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn" id="quiz-gen-btn">✨ Generate Quiz</button>
    </form>

    @if ($recent->isNotEmpty())
        <div class="card" style="margin-top:24px;">
            <div class="section-label">Recent quizzes</div>
            @foreach ($recent as $r)
                <a href="{{ route('quiz.result', $r) }}" class="dl-row" style="text-decoration:none; color:inherit;">
                    <div class="dl-left">
                        <div>
                            <div class="dl-name">{{ $r->subject }}{{ $r->topic ? ' · ' . $r->topic : '' }}</div>
                            <div class="dl-sub">{{ ucfirst($r->level) }} · {{ $r->completed_at->format('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="dl-due">{{ $r->score }}/{{ count($r->questions) }}</div>
                </a>
            @endforeach
        </div>
    @endif
@endsection

@section('scripts')
<script>
    document.getElementById('quiz-gen-form').addEventListener('submit', function () {
        const btn = document.getElementById('quiz-gen-btn');
        btn.disabled = true;
        btn.textContent = '⏳ Generating your quiz…';
    });
</script>
@endsection