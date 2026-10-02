<div class="topbar">
    <div>
        <h1>{{ $greeting ?? 'Welcome' }}, {{ auth()->user()->name ?? 'there' }}</h1>
        <div class="sub">{{ $subtitle ?? '' }}</div>
    </div>
    <div class="avatar">{{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : '?' }}</div>
</div>