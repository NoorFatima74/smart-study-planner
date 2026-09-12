@extends('layouts.app')

@section('title', 'Edit Profile')
@section('page-title', 'Profile')
@section('page-subtitle', 'Manage your account information')

@section('content')
    <div class="card" style="max-width:480px;">
        @if (session('status'))
            <p class="form-status">{{ session('status') }}</p>
        @endif

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <div class="form-group">
                <label for="name">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn">Save Changes</button>
        </form>

        <a href="{{ route('profile.password') }}" style="color:var(--text-mid); font-size:13px; margin-top:16px; display:inline-block;">
            Change password →
        </a>
    </div>
@endsection