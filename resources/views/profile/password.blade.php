@extends('layouts.app')

@section('title', 'Change Password')
@section('page-title', 'Change Password')
@section('page-subtitle', 'Update your account password')

@section('content')
    <div class="card" style="max-width:480px;">
        @if (session('status'))
            <p class="form-status">{{ session('status') }}</p>
        @endif

        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input id="current_password" type="password" name="current_password" required>
                @error('current_password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">New Password</label>
                <input id="password" type="password" name="password" required>
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn">Update Password</button>
        </form>
    </div>
@endsection