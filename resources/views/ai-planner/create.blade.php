@extends('layouts.app')

@section('title', 'AI Study Planner')
@section('page-title', 'AI Study Planner')
@section('page-subtitle', 'Generate a study plan for an upcoming exam')

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
            <label>Subject</label>
            <select name="subject_id" required>
                <option value="">Select subject</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label>What are you preparing for?</label>
            <input type="text" name="exam_name" value="{{ old('exam_name') }}" placeholder="e.g. Database Systems Exam" required>
        </div>

        <div class="field">
            <label>Exam date</label>
            <input type="date" name="exam_date" value="{{ old('exam_date') }}" required>
        </div>

        <div class="field">
            <label>Available study time per day (minutes)</label>
            <input type="number" name="daily_minutes" value="{{ old('daily_minutes', 120) }}" min="15" max="600" required>
        </div>

        <div class="field">
            <label>Topics (comma-separated)</label>
            <textarea name="topics" rows="3" placeholder="Normalization, SQL, Transactions, Indexing" required>{{ old('topics') }}</textarea>
        </div>

        <div class="field">
            <label>Current knowledge level</label>
            <select name="knowledge_level" required>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
        </div>

        <button type="submit" class="btn">✨ Generate Study Plan</button>
    </form>
@endsection