@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Welcome back, ' . auth()->user()->name)
@section('page-subtitle', $totalToday > 0
    ? "You've completed {$completedToday} of {$totalToday} tasks today"
    : 'No tasks due today')

@section('content')
    <div class="grid">

        {{-- Recommended Next --}}
        <div class="card recommend">
            @if ($recommendedTask)
                <div>
                    <span class="tag"><span class="pulse"></span>Suggested next</span>
                    <h2>{{ $recommendedTask->title }}</h2>
                    <div class="meta">
                        <b>{{ $recommendedTask->subject->name }}</b>
                        · {{ ucfirst($recommendedTask->priority) }} priority
                        @if ($recommendedTask->deadline)
                            · Due {{ $recommendedTask->deadline->format('M j') }}
                        @endif
                    </div>
                </div>
                <div>
                    <a href="{{ route('tasks.timer', $recommendedTask) }}" class="btn" style="text-decoration:none;">Start focus session</a>
                    <div class="stat-row">
                        <div>
                            <span class="num">{{ $recommendedTask->estimated_minutes ?? '—' }}{{ $recommendedTask->estimated_minutes ? 'm' : '' }}</span>
                            <span class="lbl">Estimated</span>
                        </div>
                        <div>
                            <span class="num">{{ ucfirst($recommendedTask->difficulty) }}</span>
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
        <div class="card streak">
            <div>
                <span class="flame">🔥</span>
                <div class="big" style="font-size:20px; color:var(--text-mid);">Coming soon</div>
                <div class="lbl">Streak tracking arrives in Unit 8</div>
            </div>
        </div>

        {{-- Today's study time --}}
        <div class="card time">
            <div class="section-label">Today's study time</div>
            <div style="text-align:center; margin-top:28px;">
                <div style="font-family:'Space Grotesk',sans-serif; font-size:30px; font-weight:700;">
                    {{ intdiv($studyMinutesToday, 60) }}h {{ $studyMinutesToday % 60 }}m
                </div>
                <div style="font-size:11px; color:var(--text-low); margin-top:6px;">across all sessions</div>
            </div>
        </div>

        {{-- Today's tasks --}}
        <div class="card tasks">
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
        <div class="card goal">
            <div class="section-label">Daily goal</div>
            <div style="color:var(--text-mid); font-size:13px; margin-top:16px;">Goals arrive in Unit 7</div>
        </div>

        {{-- Upcoming deadlines --}}
        <div class="card deadlines">
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