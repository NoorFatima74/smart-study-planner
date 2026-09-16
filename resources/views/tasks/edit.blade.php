@extends('layouts.app')

@section('title', 'Edit Task')
@section('page-title', 'Edit Task')
@section('page-subtitle', '')

@section('content')
    <div class="card" style="max-width:520px;">
        <form method="POST" action="{{ route('tasks.update', $task) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">Title</label>
                <input id="title" type="text" name="title" value="{{ old('title', $task->title) }}" required autofocus>
                @error('title')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3">{{ old('description', $task->description) }}</textarea>
            </div>

            <div class="form-group">
                <label for="subject_id">Subject</label>
                <select id="subject_id" name="subject_id" required>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected(old('subject_id', $task->subject_id) == $subject->id)>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
                @error('subject_id')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select id="priority" name="priority">
                        <option value="low" @selected(old('priority', $task->priority) === 'low')>Low</option>
                        <option value="medium" @selected(old('priority', $task->priority) === 'medium')>Medium</option>
                        <option value="high" @selected(old('priority', $task->priority) === 'high')>High</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="difficulty">Difficulty</label>
                    <select id="difficulty" name="difficulty">
                        <option value="easy" @selected(old('difficulty', $task->difficulty) === 'easy')>Easy</option>
                        <option value="medium" @selected(old('difficulty', $task->difficulty) === 'medium')>Medium</option>
                        <option value="hard" @selected(old('difficulty', $task->difficulty) === 'hard')>Hard</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="estimated_minutes">Estimated Minutes</label>
                    <input id="estimated_minutes" type="number" name="estimated_minutes" value="{{ old('estimated_minutes', $task->estimated_minutes) }}" min="1" max="1440">
                </div>

                <div class="form-group">
                    <label for="deadline">Deadline</label>
                    <input id="deadline" type="datetime-local" name="deadline" value="{{ old('deadline', $task->deadline?->format('Y-m-d\TH:i')) }}">
                </div>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="pending" @selected(old('status', $task->status) === 'pending')>Pending</option>
                    <option value="in_progress" @selected(old('status', $task->status) === 'in_progress')>In Progress</option>
                    <option value="completed" @selected(old('status', $task->status) === 'completed')>Completed</option>
                </select>
            </div>

            <button type="submit" class="btn">Save Changes</button>
        </form>

        <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task? This cannot be undone.');" style="margin-top:16px;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger">Delete Task</button>
        </form>
    </div>
@endsection