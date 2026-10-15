<div class="ptabs">
    <a href="{{ url('/planner') }}" class="{{ request()->is('planner*') ? 'ptab-active' : '' }}">Planner</a>
    <a href="{{ route('ai-planner.create') }}" class="{{ request()->routeIs('ai-planner.*') ? 'ptab-active' : '' }}">✨ AI Planner</a>
    <a href="{{ url('/recommendation') }}" class="{{ request()->is('recommendation*') ? 'ptab-active' : '' }}">Recommendation</a>
    <a href="{{ route('quiz.create') }}" class="{{ request()->routeIs('quiz.*') ? 'active' : '' }}">
    <span class="dot"></span>AI Quiz
</a>
</div>