@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <h2 style="text-align:center;">Welcome back</h2>

    @if (session('status'))
        <p class="form-status" style="text-align:center;">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
        </div>

        <div class="checkbox-row">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">Remember me</label>
        </div>

        <button type="submit" class="btn">Login</button>

        <div class="auth-links">
            <a href="{{ route('password.request') }}">Forgot your password?</a>
            <a href="{{ route('register') }}">Don't have an account? Register</a>
        </div>
    </form>
@endsection