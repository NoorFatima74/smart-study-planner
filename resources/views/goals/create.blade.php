@extends('layouts.app')

@section('title', 'Create Goal')
@section('page-title', 'Create Goal')
@section('page-subtitle', '')

@section('content')
    <div class="card" style="max-width:480px;">
        <form method="POST" action="{{ route('goals.store') }}">
            @csrf

            <div class="form-group">
                <label for="type">Goal Type</label>
                <select id="type" name="type" required>
                    <option value="daily" @selected(old('type') === 'daily')>Daily</option>
                    <option value="weekly" @selected(old('type', 'weekly') === 'weekly')>Weekly</option>
                    <option value="monthly" @selected(old('type') === 'monthly')>Monthly</option>
                </select>
                @error('type')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="target_minutes">Target Minutes</label>
                <input id="target_minutes" type="number" name="target_minutes" value="{{ old('target_minutes') }}" min="1" max="1440" required>
                @error('target_minutes')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input id="start_date" type="date" name="start_date" value="{{ old('start_date', now()->toDateString()) }}" required>
                    @error('start_date')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input id="end_date" type="date" name="end_date" value="{{ old('end_date', now()->toDateString()) }}" required>
                    @error('end_date')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn">Create Goal</button>
        </form>
    </div>
@endsection