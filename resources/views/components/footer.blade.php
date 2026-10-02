<footer class="site-footer">
    <div class="footer-top">
        <div class="footer-brand">
            <img src="{{ asset('images/logo.png') }}" alt="NomaEd" class="logo-img">
            <p class="footer-tagline">AI-powered productivity for students.</p>
        </div>

        <div class="footer-col">
            <div class="footer-col-title">Product</div>
            <a href="{{ url('/features') }}">Features</a>
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('tasks.index') }}">Tasks</a>
        </div>

        <div class="footer-col">
            <div class="footer-col-title">Company</div>
            <a href="{{ url('/about') }}">About</a>
            <a href="{{ url('/contact') }}">Contact</a>
        </div>

        <div class="footer-col">
            <div class="footer-col-title">Account</div>
            @auth
                <a href="{{ route('profile.edit') }}">Profile</a>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endauth
        </div>
    </div>

    <div class="footer-bottom">
        <span>&copy; {{ date('Y') }} NomaEd. All rights reserved.</span>
    </div>
</footer>