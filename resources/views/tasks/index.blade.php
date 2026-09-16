@extends('layouts.app')

@section('title', 'Tasks')
@section('page-title', 'My Tasks')
@section('page-subtitle', $tasks->total() . ' task' . ($tasks->total() === 1 ? '' : 's'))

@section('content')
    <div style="display:flex; justify-content:flex-end; margin-bottom:4px;">
        <a href="{{ route('tasks.create') }}" class="btn">+ Add Task</a>
    </div>

    @if (session('status'))
        <p class="form-status">{{ session('status') }}</p>
    @endif

    <div class="card filter-bar">
        <form method="GET" action="{{ route('tasks.index') }}" class="filter-form">
            <input type="text" name="search" placeholder="Search tasks..." value="{{ request('search') }}">

            <select name="status">
                <option value="">All statuses</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="in_progress" @selected(request('status') === 'in_progress')>In Progress</option>
                <option value="completed" @selected(request('status') === 'completed')>Completed</option>
            </select>

            <select name="priority">
                <option value="">All priorities</option>
                <option value="high" @selected(request('priority') === 'high')>High</option>
                <option value="medium" @selected(request('priority') === 'medium')>Medium</option>
                <option value="low" @selected(request('priority') === 'low')>Low</option>
            </select>

            <select name="subject">
                <option value="">All subjects</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" @selected((string) request('subject') === (string) $subject->id)>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>

            <select name="sort">
                <option value="deadline" @selected(request('sort', 'deadline') === 'deadline')>Sort: Deadline</option>
                <option value="priority" @selected(request('sort') === 'priority')>Sort: Priority</option>
                <option value="title" @selected(request('sort') === 'title')>Sort: Title</option>
                <option value="created_at" @selected(request('sort') === 'created_at')>Sort: Newest</option>
            </select>

            <button type="submit" class="btn">Filter</button>
        </form>
    </div>

    <div class="task-list">
        @forelse ($tasks as $task)
            <a href="{{ route('tasks.show', $task) }}" class="card task-row {{ $task->status === 'completed' ? 'task-row-done' : '' }}">
                <div class="task-row-main">
                    <span class="task-row-title">{{ $task->title }}</span>
                    <span class="task-row-subject">{{ $task->subject->name }}</span>
                </div>
                <div class="task-row-meta">
                    <span class="pill {{ $task->priority === 'high' ? 'high' : 'med' }}">{{ ucfirst($task->priority) }}</span>
                    <span class="task-row-deadline">
                        {{ $task->deadline ? $task->deadline->format('M j, g:i A') : 'No deadline' }}
                    </span>
                    <span class="task-row-status">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                </div>
            </a>
        @empty
            <div class="card">
                <p style="color:var(--text-mid);">No tasks match these filters.</p>
            </div>
        @endforelse
    </div>

    <div class="pagination-wrap">
        {{ $tasks->links() }}
    </div>
@endsection