@extends('layouts.app')

@section('title', 'Goals')
@section('page-title', 'My Goals')
@section('page-subtitle', $goals->count() . ' goal' . ($goals->count() === 1 ? '' : 's'))

@section('content')
    <div style="display:flex; justify-content:flex-end; margin-bottom:4px;">
        <a href="{{ route('goals.create') }}" class="btn">+ Create Goal</a>
    </div>

    @if (session('status'))
        <p class="form-status">{{ session('status') }}</p>
    @endif

    <div class="goal-list">
        @forelse ($goals as $goal)
            <div class="card goal-card">
                <div class="goal-card-header">
                    <span class="goal-type">{{ ucfirst($goal->type) }} Goal</span>
                    @if ($goal->isActive())
                        <span class="tag" style="margin:0;"><span class="pulse"></span>Active</span>
                    @endif
                </div>

                <div class="goal-target">{{ $goal->target_minutes }} min target</div>
                <div class="goal-dates">{{ $goal->start_date->format('M j') }} – {{ $goal->end_date->format('M j, Y') }}</div>

                <div class="bar-track" style="margin-top:14px;">
                    <div class="bar-fill" style="width:{{ $goal->progressPercent() }}%"></div>
                </div>
                <div class="goal-progress-text">{{ $goal->minutesLogged() }} / {{ $goal->target_minutes }} min ({{ $goal->progressPercent() }}%)</div>

                <div class="subject-actions" style="margin-top:14px;">
                    <a href="{{ route('goals.edit', $goal) }}">Edit</a>
                </div>
            </div>
        @empty
            <div class="card">
                <p style="color:var(--text-mid);">No goals yet — create one to start tracking your study time.</p>
            </div>
        @endforelse
    </div>
@endsection