@extends('layouts.app')

@section('title', 'AI Study Plan')
@section('page-title', 'Your AI Study Plan')
@section('page-subtitle', $data['exam_name'] . ' — Exam on ' . \Carbon\Carbon::parse($data['exam_date'])->format('d M Y'))

@section('content')
    <x-planner-tabs />

    @if ($errors->any())
        <div class="card" style="border-color:#e74c3c; margin-bottom:16px; color:#e74c3c; font-size:13px; padding:12px;">
            {{ $errors->first() }}
        </div>
    @endif

   @foreach ($plan['plan'] as $day)
    <div class="card" style="margin-bottom:16px; padding:16px;">
        <h2 style="font-weight:600; margin-bottom:10px; color:#fff; font-size:15px;">
            {{ \Carbon\Carbon::parse($day['date'])->format('l, d M') }}
        </h2>
        <ul style="list-style:none; padding:0; margin:0;">
            @foreach ($day['items'] as $item)
                <li style="display:flex; justify-content:space-between; align-items:flex-start; padding:10px 0; border-bottom:1px solid rgba(255,255,255,0.06); font-size:13px;">
                    <span style="flex:1;">
                        <span style="color:#fff; font-weight:500;">📚 {{ $item['title'] }}</span>
                        @if (!empty($item['reason']))
                            <span style="display:block; color:rgba(255,255,255,0.45); font-size:11.5px; margin-top:3px; font-weight:400;">
                                {{ $item['reason'] }}
                            </span>
                        @endif
                    </span>
                    <span style="color:var(--violet, #f5a623); white-space:nowrap; margin-left:12px; font-weight:600; font-size:12px;">
                        {{ $item['duration'] }} min
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
@endforeach

    <div style="display:flex; gap:12px; margin-top:20px;">
        <form method="POST" action="{{ route('ai-planner.generate') }}">
            @csrf
            <input type="hidden" name="subject_id" value="{{ $data['subject_id'] }}">
            <input type="hidden" name="exam_name" value="{{ $data['exam_name'] }}">
            <input type="hidden" name="exam_date" value="{{ $data['exam_date'] }}">
            <input type="hidden" name="daily_minutes" value="{{ $data['daily_minutes'] }}">
            <input type="hidden" name="topics" value="{{ $data['topics'] }}">
            <input type="hidden" name="knowledge_level" value="{{ $data['knowledge_level'] }}">
            <button type="submit" class="btn" style="background:rgba(255,255,255,0.08); color:#fff;">🔄 Regenerate</button>
        </form>

        <a href="{{ route('ai-planner.create') }}" class="btn" style="background:rgba(255,255,255,0.08); color:#fff; text-decoration:none;">
            ✏️ Edit Inputs
        </a>

        <form method="POST" action="{{ route('ai-planner.save') }}">
            @csrf
            <button type="submit" class="btn">✅ Add to My Planner</button>
        </form>
    </div>
@endsection