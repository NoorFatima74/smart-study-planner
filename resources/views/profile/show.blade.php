@extends('layouts.app')

@section('title', 'Profile')
@section('page-title', 'Profile')
@section('page-subtitle', $user->email)

@section('content')
    <div class="card" style="max-width:480px;">
        <div class="form-group">
            <label>Name</label>
            <div>{{ $user->name }}</div>
        </div>
        <div class="form-group">
            <label>Email</label>
            <div>{{ $user->email }}</div>
        </div>
        <a href="{{ route('profile.edit') }}" class="btn" style="margin-top:16px; display:inline-block; text-decoration:none;">Edit Profile</a>
    </div>
@endsection