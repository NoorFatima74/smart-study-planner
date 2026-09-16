@extends('layouts.app')

@section('title', 'Add Task')
@section('page-title', 'Add Task')
@section('page-subtitle', '')

@section('content')
    <div class="card" style="max-width:520px;">
        @if ($subjects->isEmpty())
            <p style="color:var(--text-mid);">
                You need at least one subject before adding a task.
                <a href="{{ route('subjects.create') }}" style="color:var(--violet);">Create one first</a>.
            </p>
        @else
            <form method="POST" action="{{ route('tasks.store') }}">
                @csrf

                <div class="form-group">
                    <label for="title">Title</label>
                    <input id="title" type="text" name="title" value="{{ old('title') }}" required autofocus>
                    @error('title')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="subject_id">Subject</label>
                    <select id="subject_id" name="subject_id" required>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>
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
                            <option value="low" @selected(old('priority') === 'low')>Low</option>
                            <option value="medium" @selected(old('priority', 'medium') === 'medium')>Medium</option>
                            <option value="high" @selected(old('priority') === 'high')>High</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="difficulty">Difficulty</label>
                        <select id="difficulty" name="difficulty">
                            <option value="easy" @selected(old('difficulty') === 'easy')>Easy</option>
                            <option value="medium" @selected(old('difficulty', 'medium') === 'medium')>Medium</option>
                            <option value="hard" @selected(old('difficulty') === 'hard')>Hard</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="estimated_minutes">Estimated Minutes</label>
                        <input id="estimated_minutes" type="number" name="estimated_minutes" value="{{ old('estimated_minutes') }}" min="1" max="1440">
                        @error('estimated_minutes')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="deadline">Deadline</label>
                        <input id="deadline" type="datetime-local" name="deadline" value="{{ old('deadline') }}">
                        @error('deadline')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn">Create Task</button>
            </form>
        @endif
    </div>
@endsection