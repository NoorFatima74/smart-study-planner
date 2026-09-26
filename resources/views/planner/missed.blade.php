
@extends('layouts.app')

@section('title', 'Missed Sessions')
@section('page-title', 'Missed Sessions')
@section('page-subtitle', '')

@section('content')
    @if (session('status'))
        <p class="form-status">{{ session('status') }}</p>
    @endif

    @if (empty($missed))
        <div class="card">
            <p style="color:var(--text-mid);">Nothing missed — you're on track.</p>
        </div>
    @else
        <div class="card" style="border-left: 3px solid #f59e0b; margin-bottom:20px;">
            <div class="section-label">⚠️ {{ count($missed) }} overdue task(s)</div>
            <div class="task-list" style="margin-top:12px;">
                @foreach ($missed as $task)
                    <div class="task-item">
                        <div class="name">{{ $task['title'] }}</div>
                        <span class="pill high">Was due {{ \Carbon\Carbon::parse($task['deadline'])->format('M j') }}</span>
                    </div>
                @endforeach
            </div>

            <form method="POST" action="{{ route('planner.reschedule') }}" style="margin-top:16px;">
                @csrf
                <label for="minutes_per_day" style="color:var(--text-mid); font-size:13px;">Available minutes per day</label>
                <input type="number" id="minutes_per_day" name="minutes_per_day" value="120" min="15" max="960">
                <button type="submit" class="btn">Redistribute & Reschedule</button>
            </form>
        </div>
    @endif
@endsection