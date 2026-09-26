<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Smart Study Planner')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <x-sidebar />

    <main>
        <div class="topbar">
            <div>
                <h1>@yield('page-title', 'Welcome')</h1>
                <div class="sub">@yield('page-subtitle', '')</div>
            </div>
            <div style="display:flex; align-items:center; gap:12px;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-link">Logout</button>
                </form>
                <div class="avatar">
                    <a href="{{ route('profile.edit') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; justify-content:center; width:100%; height:100%;">
                        {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : '?' }}
                    </a>
                </div>
            </div>
        </div>

        @yield('content')
    </main>

    @yield('scripts')

    @if (session('gamification'))
        <script>
            window.gamificationData = @json(session('gamification'));
        </script>
    @endif

</body>
</html>