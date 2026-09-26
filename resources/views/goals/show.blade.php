@extends('layouts.app')

@section('title', ucfirst($goal->type) . ' Goal')
@section('page-title', ucfirst($goal->type) . ' Goal')
@section('page-subtitle', $goal->start_date->format('M j') . ' – ' . $goal->end_date->format('M j, Y'))

@section('content')
    <div class="card" style="max-width:480px;">
        <div class="goal-target">{{ $goal->minutesLogged() }} / {{ $goal->target_minutes }} min</div>
        <div class="bar-track" style="margin-top:14px;">
            <div class="bar-fill" style="width:{{ $goal->progressPercent() }}%"></div>
        </div>
        <div class="goal-progress-text">{{ $goal->progressPercent() }}% complete</div>

        <a href="{{ route('goals.edit', $goal) }}" class="btn" style="margin-top:18px; text-decoration:none; display:inline-block;">Edit Goal</a>
    </div>
@endsection