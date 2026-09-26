@extends('layouts.app')

@section('title', 'Edit Goal')
@section('page-title', 'Edit Goal')
@section('page-subtitle', '')

@section('content')
    <div class="card" style="max-width:480px;">
        <form method="POST" action="{{ route('goals.update', $goal) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="type">Goal Type</label>
                <select id="type" name="type" required>
                    <option value="daily" @selected(old('type', $goal->type) === 'daily')>Daily</option>
                    <option value="weekly" @selected(old('type', $goal->type) === 'weekly')>Weekly</option>
                    <option value="monthly" @selected(old('type', $goal->type) === 'monthly')>Monthly</option>
                </select>
            </div>

            <div class="form-group">
                <label for="target_minutes">Target Minutes</label>
                <input id="target_minutes" type="number" name="target_minutes" value="{{ old('target_minutes', $goal->target_minutes) }}" min="1" max="1440" required>
                @error('target_minutes')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input id="start_date" type="date" name="start_date" value="{{ old('start_date', $goal->start_date->toDateString()) }}" required>
                </div>

                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input id="end_date" type="date" name="end_date" value="{{ old('end_date', $goal->end_date->toDateString()) }}" required>
                    @error('end_date')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn">Save Changes</button>
        </form>

        <form method="POST" action="{{ route('goals.destroy', $goal) }}" onsubmit="return confirm('Delete this goal?');" style="margin-top:16px;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger">Delete Goal</button>
        </form>
    </div>
@endsection