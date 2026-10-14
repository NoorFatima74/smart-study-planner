@extends('layouts.app')

@php
    $isSingle = $data['plan_type'] === 'single';
    $hl = fn ($t) => preg_replace('/\*\*(.+?)\*\*/', '<span class="hl">$1</span>', e($t));
@endphp

@section('title', 'AI Study Plan')
@section('page-title', 'Your AI Study Plan')
@section('page-subtitle', $data['goal'] . ' · ' . ($isSingle
    ? $data['session_minutes'] . ' min session'
    : 'by ' . \Carbon\Carbon::parse($data['deadline'])->format('d M Y')))

@section('content')
    <x-planner-tabs />

    @if ($errors->any())
        <div class="card" style="border-color:#e74c3c; margin-bottom:16px; color:#e74c3c; font-size:13px; padding:12px;">
            {{ $errors->first() }}
        </div>
    @endif

    @foreach ($plan['plan'] as $day)
        <div class="card plan-day">
            <div class="section-label" style="margin-bottom:10px;">
                {{ $isSingle ? 'Your session' : \Carbon\Carbon::parse($day['date'])->format('l, d M') }}
                <span class="more" style="color:var(--violet);">{{ collect($day['items'])->sum('duration') }} min total</span>
            </div>

            <div class="plan-table-wrap">
                <table class="plan-table">
                    <thead>
                        <tr>
                            @if ($isSingle)<th>Time</th>@endif
                            <th>Activity</th>
                            <th>What to do</th>
                            <th>Why it helps</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $t = 0; @endphp
                        @foreach ($day['items'] as $item)
                            <tr>
                                @if ($isSingle)
                                    <td class="plan-time">{{ $t }}–{{ $t + $item['duration'] }} min</td>
                                    @php $t += $item['duration']; @endphp
                                @endif
                                <td>
                                    <span class="chip chip-{{ strtolower($item['activity']) }}">{{ $item['activity'] }}</span>
                                    <div class="plan-title">{!! $hl($item['title']) !!}</div>
                                </td>
                                <td>
                                    <ul class="plan-steps">
                                        @foreach ($item['steps'] as $step)
                                            <li>{!! $hl($step) !!}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="plan-why">{!! $hl($item['why']) !!}</td>
                                <td class="plan-duration">{{ $item['duration'] }} min</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    <div style="display:flex; gap:12px; margin-top:20px; flex-wrap:wrap;">
        <form method="POST" action="{{ route('ai-planner.generate') }}">
            @csrf
            @foreach ($data as $key => $value)
                @if (!is_null($value))
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            <button type="submit" class="btn btn-secondary">🔄 Regenerate</button>
        </form>

        <a href="{{ route('ai-planner.create') }}" class="btn btn-secondary" style="text-decoration:none;">✏️ Edit Inputs</a>

        <form method="POST" action="{{ route('ai-planner.save') }}">
            @csrf
            <button type="submit" class="btn">✅ Add to My Planner</button>
        </form>
    </div>
@endsection