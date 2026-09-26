{{-- resources/views/achievements/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Achievements')
@section('page-title', 'Achievements')
@section('page-subtitle', 'Level ' . $user->level . ' · ' . $user->xp . ' XP')

@section('content')
    <div class="card level-card">
        <div class="level-progress-bar">
            <div class="level-progress-fill" style="width: {{ $user->levelProgressPercent() }}%"></div>
        </div>
        <p>{{ $user->xpToNextLevel() }} XP to Level {{ $user->level + 1 }}</p>
    </div>

    <div class="achievements-grid">
        @foreach ($achievements as $achievement)
            <div class="card achievement-card {{ $achievement->earned ? 'earned' : 'locked' }}">
                <div class="achievement-icon">{{ $achievement->icon }}</div>
                <h3>{{ $achievement->name }}</h3>
                <p>{{ $achievement->description }}</p>
                @if ($achievement->earned)
                    <span class="badge-teal">Earned</span>
                @else
                    <span class="badge-locked">Locked</span>
                @endif
            </div>
        @endforeach
    </div>
@endsection