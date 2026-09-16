@extends('layouts.app')

@section('title', 'Edit Subject')
@section('page-title', 'Edit Subject')
@section('page-subtitle', '')

@section('content')
    <div class="card" style="max-width:480px;">
        <form method="POST" action="{{ route('subjects.update', $subject) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Subject Name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $subject->name) }}" required autofocus>
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4">{{ old('description', $subject->description) }}</textarea>
                @error('description')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn">Save Changes</button>
        </form>

        <form method="POST" action="{{ route('subjects.destroy', $subject) }}" onsubmit="return confirm('Delete this subject? This cannot be undone.');" style="margin-top:16px;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger">Delete Subject</button>
        </form>
    </div>
@endsection