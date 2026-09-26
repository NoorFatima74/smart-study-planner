@extends('layouts.app')

@section('title', 'Add Subject')
@section('page-title', 'Add Subject')
@section('page-subtitle', '')

@section('content')
<a href="{{ route('subjects.index') }}" class="back-link">← Back to subjects</a>
    <div class="card" style="max-width:480px;">
        <form method="POST" action="{{ route('subjects.store') }}">
            @csrf

            <div class="form-group">
                <label for="name">Subject Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn">Create Subject</button>
        </form>
    </div>
@endsection