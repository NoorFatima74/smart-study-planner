@extends('layouts.app')

@section('title', $subject->name)
@section('page-title', $subject->name)
@section('page-subtitle', $subject->description ?? '')

@section('content')
    <div class="card">
        <h3 style="margin-bottom:12px;">Tasks</h3>

        @forelse ($subject->tasks()->latest()->get() as $task)
            <a href="{{ route('tasks.show', $task) }}" class="card task-row" style="margin-bottom:10px;">
                <div class="task-row-main">
                    <span class="task-row-title">{{ $task->title }}</span>
                </div>
                <div class="task-row-meta">
                    <span class="pill {{ $task->priority === 'high' ? 'high' : 'med' }}">{{ ucfirst($task->priority) }}</span>
                    <span class="task-row-status">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                </div>
            </a>
        @empty
            <p style="color:var(--text-mid); font-size:13.5px;">No tasks in this subject yet.</p>
        @endforelse
    </div>

    <a href="{{ route('subjects.edit', $subject) }}" class="btn" style="margin-top:16px; text-decoration:none; display:inline-block;">Edit Subject</a>
@endsection