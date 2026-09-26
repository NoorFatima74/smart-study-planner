@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Welcome back, ' . auth()->user()->name)
@section('page-subtitle', $totalToday > 0
    ? "You've completed {$completedToday} of {$totalToday} tasks today"
    : 'No tasks due today')

@section('content')
    <div class="grid" style="grid-template-columns: repeat(4, 1fr); grid-auto-rows: min-content;">

       {{-- Recommended Next --}}
<div class="card recommend" style="grid-column: span 2;">
    @if ($topRecommendation)
        <div>
            <span class="tag"><span class="pulse"></span>Suggested next</span>
            <h2>{{ $topRecommendation['task']->title }}</h2>
            <div class="meta">
                <b>{{ $topRecommendation['task']->subject->name }}</b>
                · {{ ucfirst($topRecommendation['task']->priority) }} priority
                @if ($topRecommendation['task']->deadline)
                    · Due {{ $topRecommendation['task']->deadline->format('M j') }}
                @endif
            </div>
            <div style="margin-top:6px;">
                @foreach ($topRecommendation['reasons'] as $reason)
                    <span class="pill" style="margin-right:4px;">{{ $reason }}</span>
                @endforeach
            </div>
        </div>
        <div>
            <a href="{{ route('tasks.timer', $topRecommendation['task']) }}" class="btn" style="text-decoration:none;">Start focus session</a>
            <div class="stat-row">
                <div>
                    <span class="num">{{ $topRecommendation['task']->estimated_minutes ?? '—' }}{{ $topRecommendation['task']->estimated_minutes ? 'm' : '' }}</span>
                    <span class="lbl">Estimated</span>
                </div>
                <div>
                    <span class="num">{{ ucfirst($topRecommendation['task']->difficulty) }}</span>
                    <span class="lbl">Difficulty</span>
                </div>
            </div>
        </div>
    @else
        <div>
            <span class="tag">Nothing pending</span>
            <h2>No open tasks right now</h2>
            <div class="meta">Add a task to get a suggestion here.</div>
        </div>
        <a href="{{ route('tasks.create') }}" class="btn" style="text-decoration:none; width:fit-content;">Add a task</a>
    @endif
</div>

        {{-- Streak (placeholder — built in Unit 8) --}}
       <div class="card streak" style="grid-column: span 1;">
    <div>
        <span class="flame">🔥</span>
        <div class="big">{{ $currentStreak }}</div>
        <div class="lbl">day{{ $currentStreak === 1 ? '' : 's' }} · best {{ $longestStreak }}</div>
    </div>
</div>

        {{-- Today's study time --}}
        <div class="card time" style="grid-column: span 1;">
            <div class="section-label">Today's study time</div>
            <div style="text-align:center; margin-top:28px;">
                <div style="font-family:'Space Grotesk',sans-serif; font-size:30px; font-weight:700;">
                    {{ intdiv($studyMinutesToday, 60) }}h {{ $studyMinutesToday % 60 }}m
                </div>
                <div style="font-size:11px; color:var(--text-low); margin-top:6px;">across all sessions</div>
            </div>
        </div>

        {{-- Today's tasks --}}
        <div class="card tasks" style="grid-column: span 2;">
            <div class="section-label">
                Today's tasks
                <span class="more"><a href="{{ route('tasks.index') }}" style="color:inherit; text-decoration:none;">View all</a></span>
            </div>
            <div class="task-list">
                @forelse ($todayTasks as $task)
                    <a href="{{ route('tasks.show', $task) }}" class="task-item {{ $task->status === 'completed' ? 'done' : '' }}" style="text-decoration:none; color:inherit;">
                        <div class="check {{ $task->status === 'completed' ? 'done' : '' }}"></div>
                        <div class="name">{{ $task->title }}</div>
                        @if ($task->status !== 'completed')
                            <span class="pill {{ $task->priority === 'high' ? 'high' : 'med' }}">{{ ucfirst($task->priority) }}</span>
                        @endif
                    </a>
                @empty
                    <p style="color:var(--text-mid); font-size:13px;">Nothing due today.</p>
                @endforelse
            </div>
        </div>

        {{-- Goal (placeholder — built in Unit 7) --}}
        {{-- Goal --}}
<div class="card goal">
    <div class="section-label">{{ $activeGoal ? ucfirst($activeGoal->type) . ' goal' : 'Goal' }}</div>
    @if ($activeGoal)
        <div class="bar-track" style="margin-top:16px;">
            <div class="bar-fill" style="width:{{ $activeGoal->progressPercent() }}%"></div>
        </div>
        <div class="num" style="margin-top:12px;">{{ $activeGoal->minutesLogged() }} / {{ $activeGoal->target_minutes }}m</div>
        <div class="lbl">{{ $activeGoal->progressPercent() }}% complete</div>
    @else
        <div style="color:var(--text-mid); font-size:13px; margin-top:16px;">
            No active goal — <a href="{{ route('goals.create') }}" style="color:var(--violet);">create one</a>.
        </div>
    @endif
</div>

        {{-- Upcoming deadlines --}}
        <div class="card deadlines" style="grid-column: span 1;">
            <div class="section-label">
                Upcoming deadlines
                <span class="more"><a href="{{ route('tasks.index') }}" style="color:inherit; text-decoration:none;">View all</a></span>
            </div>
            @forelse ($upcomingDeadlines as $task)
                <a href="{{ route('tasks.show', $task) }}" class="dl-row" style="text-decoration:none; color:inherit;">
                    <div class="dl-left">
                        <div class="dl-icon">📘</div>
                        <div>
                            <div class="dl-name">{{ $task->title }}</div>
                            <div class="dl-sub">{{ $task->subject->name }}</div>
                        </div>
                    </div>
                    <div class="dl-due {{ $task->deadline->isToday() || $task->deadline->isTomorrow() ? 'soon' : '' }}">
                        {{ $task->deadline->format('M j, g:i A') }}
                    </div>
                </a>
            @empty
                <p style="color:var(--text-mid); font-size:13px;">No upcoming deadlines.</p>
            @endforelse
        </div>

    </div>
@endsection