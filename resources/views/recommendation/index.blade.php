
@extends('layouts.app')

@section('title', 'Recommendation')
@section('page-title', 'What Should I Study Next?')
@section('page-subtitle', '')

@section('content')
    @if (empty($ranked))
        <div class="card">
            <p style="color:var(--text-mid);">No pending tasks to recommend. Add a task to get started.</p>
        </div>
    @else
      <div class="card" style="margin-bottom:20px; border-left: 3px solid #8B7FD6; max-width:760px;">
            <p style="font-size:0.85rem; color:var(--text-mid); margin-bottom:4px;">🧠 Top Recommendation</p>
            <h2 style="margin-bottom:8px;">{{ $ranked[0]['task']->title }}</h2>
            <p style="color:var(--text-mid); margin-bottom:8px;">{{ $ranked[0]['task']->subject->name }}</p>
            
            <div style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:16px;">
    @foreach ($ranked[0]['reasons'] as $reason)
        <span class="pill">{{ $reason }}</span>
    @endforeach
</div>
<a href="{{ route('tasks.show', $ranked[0]['task']) }}" class="btn" style="display:inline-block; clear:both;">View Task</a>
        </div>

        @if (count($ranked) > 1)
            <h3 style="margin-bottom:12px;">Also worth considering</h3>
            <div class="task-list">
                @foreach (array_slice($ranked, 1) as $item)
                    <a href="{{ route('tasks.show', $item['task']) }}" class="card task-row">
                        <div class="task-row-main">
                            <span class="task-row-title">{{ $item['task']->title }}</span>
                            <span class="task-row-subject">{{ $item['task']->subject->name }}</span>
                        </div>
                        <div class="task-row-meta">
                            @foreach ($item['reasons'] as $reason)
                                <span class="pill">{{ $reason }}</span>
                            @endforeach
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    @endif
@endsection