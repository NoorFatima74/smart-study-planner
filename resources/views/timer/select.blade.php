@extends('layouts.app')

@section('title', 'Focus Timer')
@section('page-title', 'Focus Timer')
@section('page-subtitle', 'Pick a task to start studying')

@section('content')
    <div class="task-list">
        @forelse ($tasks as $task)
            <a href="{{ route('tasks.timer', $task) }}" class="card task-row">
                <div class="task-row-main">
                    <span class="task-row-title">{{ $task->title }}</span>
                    <span class="task-row-subject">{{ $task->subject->name }}</span>
                </div>
                <div class="task-row-meta">
                    <span class="pill {{ $task->priority === 'high' ? 'high' : 'med' }}">{{ ucfirst($task->priority) }}</span>
                    <span class="task-row-deadline">
                        {{ $task->deadline ? $task->deadline->format('M j, g:i A') : 'No deadline' }}
                    </span>
                </div>
            </a>
        @empty
            <div class="card">
                <p style="color:var(--text-mid);">No open tasks to study. <a href="{{ route('tasks.create') }}" style="color:var(--violet);">Add one</a>.</p>
            </div>
        @endforelse
    </div>
@endsection