<nav class="navbar">
    <a href="{{ url('/') }}" class="brand"><span class="brand-mark"></span>Planner</a>
    <div class="navbar-links">
        <a href="{{ url('/features') }}">Features</a>
        <a href="{{ url('/about') }}">About</a>
        <a href="{{ url('/contact') }}">Contact</a>
    </div>
    <div class="navbar-actions">
        @auth
            <a href="{{ route('dashboard') }}" class="btn">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="navbar-link">Login</a>
            <a href="{{ route('register') }}" class="btn">Get Started</a>
        @endauth
    </div>
</nav>