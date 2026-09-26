@extends('layouts.app')

@section('title', $task->title)
@section('page-title', $task->title)
@section('page-subtitle', $task->subject->name)

@section('content')
    <div class="card task-detail">
        <div class="task-detail-grid">
            <div><span class="label">Priority</span><span class="pill {{ $task->priority === 'high' ? 'high' : 'med' }}">{{ ucfirst($task->priority) }}</span></div>
            <div><span class="label">Difficulty</span><span>{{ ucfirst($task->difficulty) }}</span></div>
            <div><span class="label">Estimated</span><span>{{ $task->estimated_minutes ? $task->estimated_minutes . ' min' : '—' }}</span></div>
            <div>
          <span class="label">Deadline</span>
          <span>{{ $task->deadline ? $task->deadline->format('M j, Y g:i A') : 'No deadline' }}</span>
         @if ($task->reschedule_count > 0)
           <div style="color:var(--text-mid); font-size:12px; margin-top:2px;">
            ⚠️ Rescheduled {{ $task->reschedule_count }}x (originally {{ $task->original_deadline->format('M j') }})
         </div>
        @endif
       </div>
            <div><span class="label">Status</span><span>{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span></div>
        </div>

        @if ($task->description)
            <p style="color:var(--text-mid); margin-top:16px;">{{ $task->description }}</p>
        @endif

        <div style="display:flex; gap:12px; margin-top:20px;">
           <div style="display:flex; gap:12px; margin-top:20px;">
    @if ($task->status !== 'completed')
        <a href="{{ route('tasks.timer', $task) }}" class="btn" style="text-decoration:none;">Start Studying</a>
    @endif
    <a href="{{ route('tasks.edit', $task) }}" class="btn" style="text-decoration:none;">Edit</a>

    @if ($task->status !== 'completed')
        <form method="POST" action="{{ route('tasks.update', $task) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="title" value="{{ $task->title }}">
            <input type="hidden" name="subject_id" value="{{ $task->subject_id }}">
            <input type="hidden" name="priority" value="{{ $task->priority }}">
            <input type="hidden" name="difficulty" value="{{ $task->difficulty }}">
            <input type="hidden" name="estimated_minutes" value="{{ $task->estimated_minutes }}">
            <input type="hidden" name="deadline" value="{{ $task->deadline?->format('Y-m-d\TH:i') }}">
            <input type="hidden" name="status" value="completed">
            <button type="submit" class="btn">Mark Complete</button>
        </form>
    @endif
</div>
    </div>

    <div class="card" style="margin-top:18px;">
    <h3 style="margin-bottom:8px;">Study Sessions</h3>
    @forelse ($task->studySessions()->whereNotNull('ended_at')->latest('started_at')->get() as $session)
        <div class="session-row" style="border-bottom:1px solid var(--glass-border); padding:10px 0;">
            <span>{{ $session->started_at->format('M j, g:i A') }}</span>
            <span style="color:var(--text-mid); margin-left:12px;">{{ $session->duration_minutes }} min — {{ ucfirst($session->status) }}</span>
        </div>
    @empty
        <p style="color:var(--text-mid); font-size:13.5px;">No sessions logged yet.</p>
    @endforelse
</div>
@endsection