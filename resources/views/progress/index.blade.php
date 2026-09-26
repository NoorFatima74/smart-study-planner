@extends('layouts.app')

@section('title', 'Progress')
@section('page-title', 'Your Progress')
@section('page-subtitle', '')

@section('content')
    <div class="grid">
        <div class="card" style="grid-column: span 1;">
            <div class="section-label">Tasks Completed</div>
            <div class="progress-stat">{{ $tasksCompleted }}</div>
            <div class="progress-stat-sub">of {{ $totalTasks }} total</div>
        </div>

        <div class="card" style="grid-column: span 1;">
            <div class="section-label">Study Time</div>
            <div class="progress-stat">{{ intdiv($totalStudyMinutes, 60) }}h {{ $totalStudyMinutes % 60 }}m</div>
            <div class="progress-stat-sub">all time</div>
        </div>

        <div class="card" style="grid-column: span 1;">
            <div class="section-label">Completion Rate</div>
            <div class="progress-stat">{{ $completionRate }}%</div>
            <div class="progress-stat-sub">tasks finished</div>
        </div>

        <div class="card" style="grid-column: span 1;">
    <div class="section-label">Current Streak</div>
    <div class="progress-stat">{{ $currentStreak }} day{{ $currentStreak === 1 ? '' : 's' }}</div>
    <div class="progress-stat-sub">longest: {{ $longestStreak }} day{{ $longestStreak === 1 ? '' : 's' }}</div>
</div>

        <div class="card" style="grid-column: span 2;">
            <div class="section-label">Most Studied Subject</div>
            <div class="progress-stat">{{ $mostStudiedSubject->name ?? '—' }}</div>
            @if ($mostStudiedSubject)
                <div class="progress-stat-sub">{{ intdiv($mostStudiedSubject->total_minutes, 60) }}h {{ $mostStudiedSubject->total_minutes % 60 }}m logged</div>
            @endif
        </div>
    </div>
@endsection