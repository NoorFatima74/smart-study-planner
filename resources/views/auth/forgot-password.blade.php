@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <h2 style="text-align:center;">Reset your password</h2>
    <p style="color:var(--text-mid); font-size:13.5px; text-align:center;">
        Enter your email and we'll send you a reset link.
    </p>

    @if (session('status'))
        <p class="form-status" style="text-align:center;">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn">Send Reset Link</button>

        <div class="auth-links">
            <a href="{{ route('login') }}">Back to login</a>
        </div>
    </form>
@endsection