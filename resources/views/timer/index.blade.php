@extends('layouts.app')

@section('title', 'Focus Timer')
@section('page-title', 'Focus Session')
@section('page-subtitle', $task->subject->name)

@php
    $totalSeconds = ($task->estimated_minutes ?? 25) * 60;
@endphp

@section('content')

    <a href="{{ route('tasks.show', $task) }}" class="timer-back-link">← Back to task</a>

    <div class="card timer-card"
     id="timer-card"
     data-task-id="{{ $task->id }}"
     data-total-seconds="{{ $totalSeconds }}"
     data-active-session-id="{{ $activeSession?->id }}"
     data-started-at="{{ $activeSession?->started_at?->toIso8601String() }}"
     data-paused-at="{{ $activeSession?->paused_at?->toIso8601String() }}"
     data-paused-seconds="{{ $activeSession?->paused_seconds ?? 0 }}">

        <div class="timer-task-name">{{ $task->title }}</div>
        <div class="timer-task-meta">
            <span class="pill {{ $task->priority === 'high' ? 'high' : 'med' }}">{{ ucfirst($task->priority) }}</span>
            <span>{{ $task->estimated_minutes ?? 25 }} min planned</span>
        </div>

        <div class="timer-display" id="timer-display">
            {{ gmdate('i:s', $totalSeconds) }}
        </div>

        <div class="timer-controls" id="timer-controls-idle">
            <button type="button" class="btn" id="btn-start">Start Focus Session</button>
        </div>

        <div class="timer-controls" id="timer-controls-active" style="display:none;">
            <button type="button" class="btn-secondary" id="btn-pause">Pause</button>
            <button type="button" class="btn" id="btn-finish">Finish Session</button>
            <button type="button" class="btn-danger" id="btn-cancel">Cancel</button>
        </div>

        <div class="timer-complete" id="timer-complete" style="display:none;">
            <div class="timer-complete-icon">🎉</div>
            <div class="timer-complete-text" id="timer-complete-text">Session Completed</div>
            <a href="{{ route('tasks.show', $task) }}" class="btn" style="text-decoration:none;">Back to Task</a>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/timer.js'])
@endsection