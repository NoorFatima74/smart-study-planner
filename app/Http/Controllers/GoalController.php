<?php

namespace App\Http\Controllers;

use App\Http\Requests\Goal\StoreGoalRequest;
use App\Http\Requests\Goal\UpdateGoalRequest;
use App\Models\Goal;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::where('user_id', Auth::id())
            ->orderByDesc('start_date')
            ->get();

        return view('goals.index', compact('goals'));
    }

    public function create()
    {
        return view('goals.create');
    }

    public function store(StoreGoalRequest $request)
    {
        Auth::user()->goals()->create($request->validated());

        return redirect()->route('goals.index')->with('status', 'Goal created.');
    }

    public function show(Goal $goal)
    {
        $this->authorize('view', $goal);

        return view('goals.show', compact('goal'));
    }

    public function edit(Goal $goal)
    {
        $this->authorize('update', $goal);

        return view('goals.edit', compact('goal'));
    }

    public function update(UpdateGoalRequest $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $goal->update($request->validated());

        return redirect()->route('goals.index')->with('status', 'Goal updated.');
    }

    public function destroy(Goal $goal)
    {
        $this->authorize('delete', $goal);

        $goal->delete();

        return redirect()->route('goals.index')->with('status', 'Goal deleted.');
    }
}