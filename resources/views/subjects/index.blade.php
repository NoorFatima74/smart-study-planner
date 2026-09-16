@extends('layouts.app')

@section('title', 'Subjects')
@section('page-title', 'My Subjects')
@section('page-subtitle', $subjects->count() . ' subject' . ($subjects->count() === 1 ? '' : 's'))

@section('content')
    <div style="display:flex; justify-content:flex-end; margin-bottom:4px;">
        <a href="{{ route('subjects.create') }}" class="btn">+ Add Subject</a>
    </div>

    @if (session('status'))
        <p class="form-status">{{ session('status') }}</p>
    @endif

    <div class="subject-list">
        @forelse ($subjects as $subject)
            <div class="card subject-card">
                <div>
                    <h3>{{ $subject->name }}</h3>
                    @if ($subject->description)
                        <p class="subject-desc">{{ $subject->description }}</p>
                    @endif
                </div>
                <div class="subject-meta">
                    <span>— tasks</span>
                    <span>— studied</span>
                </div>
                <div class="subject-actions">
                    <a href="{{ route('subjects.show', $subject) }}">View</a>
                    <a href="{{ route('subjects.edit', $subject) }}">Edit</a>
                </div>
            </div>
        @empty
            <div class="card">
                <p style="color:var(--text-mid);">No subjects yet — add your first one to get started.</p>
            </div>
        @endforelse
    </div>
@endsection