@extends('layouts.app')

@section('title', 'Study Sessions')
@section('page-title', 'Study History')
@section('page-subtitle', $sessions->total() . ' session' . ($sessions->total() === 1 ? '' : 's'))

@section('content')
    <div class="session-list">
        @forelse ($sessions as $session)
            <div class="card session-row">
                <div class="session-row-main">
                    <span class="session-row-title">{{ $session->task->title }}</span>
                    <span class="session-row-subject">{{ $session->task->subject->name }}</span>
                </div>
                <div class="session-row-meta">
                    <span>{{ $session->started_at->format('M j, g:i A') }}</span>
                    <span class="session-duration">{{ $session->duration_minutes }} min</span>
                    <span class="pill {{ $session->status === 'completed' ? 'med' : 'high' }}">
                        {{ ucfirst($session->status) }}
                    </span>
                </div>
            </div>
        @empty
            <div class="card">
                <p style="color:var(--text-mid);">No study sessions yet — start a focus session from any task.</p>
            </div>
        @endforelse
    </div>

    <div class="pagination-wrap">
        {{ $sessions->links() }}
    </div>
@endsection