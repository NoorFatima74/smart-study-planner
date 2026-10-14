@extends('layouts.app')

@section('title', 'AI Study Planner')
@section('page-title', 'AI Study Planner')
@section('page-subtitle', 'Plan anything: a 10-minute revision or a multi-day goal')

@section('content')
    <x-planner-tabs />

    <h1 style="font-family:'Space Grotesk',sans-serif; font-size:22px; margin-bottom:20px;">✨ AI Study Planner</h1>

    @if ($errors->any())
        <div class="card" style="border-color:#e74c3c; margin-bottom:16px; color:#e74c3c; font-size:13px; padding:12px;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('ai-planner.generate') }}" class="ai-form">
        @csrf

        <div class="field">
            <label>What do you want to plan?</label>
            <input type="text" name="goal" value="{{ old('goal') }}" placeholder="e.g. Learn SQL joins, revise Chapter 3, prepare a presentation" required>
        </div>

        <div class="field">
            <label>Plan type</label>
            <select name="plan_type" id="plan_type">
                <option value="single" {{ old('plan_type', 'single') === 'single' ? 'selected' : '' }}>Quick session (one sitting)</option>
                <option value="multi" {{ old('plan_type') === 'multi' ? 'selected' : '' }}>Multi-day plan</option>
            </select>
        </div>

        <div id="single-fields">
            <div class="field">
                <label>How much time do you have? (minutes)</label>
                <input type="number" name="session_minutes" value="{{ old('session_minutes', 30) }}" min="5" max="480">
            </div>
        </div>

        <div id="multi-fields">
            <div class="field">
                <label>Deadline</label>
                <input type="date" name="deadline" value="{{ old('deadline') }}">
            </div>
            <div class="field">
                <label>Available time per day (minutes)</label>
                <input type="number" name="daily_minutes" value="{{ old('daily_minutes', 60) }}" min="10" max="600">
            </div>
        </div>

        <div class="field">
            <label>Topics or details (optional)</label>
            <textarea name="topics" rows="3" placeholder="Anything specific you want covered">{{ old('topics') }}</textarea>
        </div>

        <div class="field">
            <label>Subject (optional)</label>
            <select name="subject_id">
                <option value="">No subject (save under General)</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label>Current knowledge level</label>
            <select name="knowledge_level" required>
                @foreach (['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'] as $v => $l)
                    <option value="{{ $v }}" {{ old('knowledge_level') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn">✨ Generate Plan</button>
    </form>
@endsection

@section('scripts')
<script>
    const planType = document.getElementById('plan_type');
    function togglePlanFields() {
        const single = planType.value === 'single';
        [['single-fields', single], ['multi-fields', !single]].forEach(([id, show]) => {
            const box = document.getElementById(id);
            box.style.display = show ? 'block' : 'none';
            box.querySelectorAll('input').forEach(i => i.disabled = !show);
        });
    }
    planType.addEventListener('change', togglePlanFields);
    togglePlanFields();
</script>
@endsection