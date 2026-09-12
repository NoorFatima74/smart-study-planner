@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Welcome, ' . auth()->user()->name)
@section('page-subtitle', 'This is a placeholder — the full dashboard comes in Unit 6')

@section('content')
    <div class="card">
        <p>You're logged in. The full dashboard is built in Unit 6.</p>
    </div>
@endsection
<form method="POST" action="{{ route('logout') }}">
    @csrf

    <button type="submit">
        Logout
    </button>
</form>