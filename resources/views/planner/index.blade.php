
@extends('layouts.app')

@section('title', 'Planner')
@section('page-title', 'Study Planner')
@section('page-subtitle', 'Suggested schedule based on your deadlines')

@section('content')
<div style="margin-bottom:16px;">
    <a href="{{ route('planner.missed') }}" class="btn-danger" style="text-decoration:none; display:inline-block;">Check missed sessions</a>
</div>
    <div class="card filter-bar" style="margin-bottom:20px;">
        <form method="GET" action="{{ route('planner.index') }}" class="filter-form">
            <label for="minutes_per_day" style="align-self:center; color:var(--text-mid); font-size:13px;">
                Available minutes per day
            </label>
            <input type="number" id="minutes_per_day" name="minutes_per_day" value="{{ $minutesPerDay }}" min="15" max="960">
            <button type="submit" class="btn">Update Plan</button>
        </form>
    </div>

    @if (empty($plan['days']) && empty($plan['unscheduled']))
        <div class="card">
            <p style="color:var(--text-mid);">No tasks with deadlines and estimated time to plan. Add estimated time and a deadline to your tasks first.</p>
        </div>
    @endif

     @foreach ($plan['days'] as $day)
    <div class="card plan-day">
        <div class="section-label">
            {{ $day['date']->isToday() ? 'Today' : $day['date']->format('D, M j') }}
        </div>
        <div class="task-list">
            @foreach ($day['entries'] as $entry)
                <a href="{{ route('tasks.show', $entry['task']) }}" class="task-item" style="text-decoration:none; color:inherit;">
                    <div class="name">{{ $entry['task']->title }}</div>
                    <span class="pill">{{ $entry['minutes'] }} min</span>
                </a>
            @endforeach
        </div>
    </div>
@endforeach

@if (!empty($plan['unscheduled']))
    <div class="card plan-day plan-day-warn">
        <div class="section-label">⚠️ Not enough time before deadline</div>
        <div class="task-list">
            @foreach ($plan['unscheduled'] as $item)
                <a href="{{ route('tasks.show', $item['task']) }}" class="task-item" style="text-decoration:none; color:inherit;">
                    <div class="name">{{ $item['task']->title }}</div>
                    <span class="pill high">{{ $item['minutes_short'] }} min short</span>
                </a>
            @endforeach
        </div>
    </div>
@endif
@endsection