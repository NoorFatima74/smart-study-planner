<div class="ptabs">
    <a href="{{ url('/planner') }}" class="{{ request()->is('planner*') ? 'ptab-active' : '' }}">Planner</a>
    <a href="{{ route('ai-planner.create') }}" class="{{ request()->routeIs('ai-planner.*') ? 'ptab-active' : '' }}">✨ AI Planner</a>
    <a href="{{ url('/recommendation') }}" class="{{ request()->is('recommendation*') ? 'ptab-active' : '' }}">Recommendation</a>
</div>