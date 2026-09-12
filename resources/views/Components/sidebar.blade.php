<aside class="sidebar">
    <div class="brand"><span class="brand-mark"></span>Planner</div>
    <nav>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="dot"></span>Dashboard
        </a>
        <a href="{{ url('/subjects') }}" class="{{ request()->is('subjects*') ? 'active' : '' }}">
            <span class="dot"></span>Subjects
        </a>
        <a href="{{ url('/tasks') }}" class="{{ request()->is('tasks*') ? 'active' : '' }}">
            <span class="dot"></span>Tasks
        </a>
        <a href="{{ url('/timer') }}" class="{{ request()->is('timer*') ? 'active' : '' }}">
            <span class="dot"></span>Timer
        </a>
        <a href="{{ url('/study-sessions') }}" class="{{ request()->is('study-sessions*') ? 'active' : '' }}">
            <span class="dot"></span>Study Sessions
        </a>
        <a href="{{ url('/planner') }}" class="{{ request()->is('planner*') ? 'active' : '' }}">
            <span class="dot"></span>Planner
        </a>
        <a href="{{ url('/recommendation') }}" class="{{ request()->is('recommendation*') ? 'active' : '' }}">
            <span class="dot"></span>Recommendation
        </a>
        <a href="{{ url('/goals') }}" class="{{ request()->is('goals*') ? 'active' : '' }}">
            <span class="dot"></span>Goals
        </a>
        <a href="{{ url('/progress') }}" class="{{ request()->is('progress*') ? 'active' : '' }}">
            <span class="dot"></span>Progress
        </a>
        <a href="{{ url('/achievements') }}" class="{{ request()->is('achievements*') ? 'active' : '' }}">
            <span class="dot"></span>Achievements
        </a>
        <a href="{{ url('/analytics') }}" class="{{ request()->is('analytics*') ? 'active' : '' }}">
            <span class="dot"></span>Analytics
        </a>
    </nav>
    <div class="sidebar-foot">
        <div class="label">Next session</div>
        <div class="value">{{ $nextSession ?? 'No upcoming session' }}</div>
    </div>
</aside>