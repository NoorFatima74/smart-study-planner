<?php

namespace App\Http\Controllers;

use App\Models\StudySession;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudySessionController extends Controller
{
    public function index()
    {
        $sessions = StudySession::where('user_id', Auth::id())
            ->with('task.subject')
            ->whereNotNull('ended_at')
            ->latest('started_at')
            ->paginate(15);

        return view('study-sessions.index', compact('sessions'));
    }

    public function select()
    {
        $tasks = Task::where('user_id', Auth::id())
            ->where('status', '!=', 'completed')
            ->with('subject')
            ->orderBy('deadline')
            ->get();

        return view('timer.select', compact('tasks'));
    }

    public function timer(Task $task)
    {
        $this->authorize('view', $task);

        $activeSession = StudySession::where('user_id', Auth::id())
            ->where('task_id', $task->id)
            ->where('status', 'in_progress')
            ->latest('started_at')
            ->first();

        return view('timer.index', compact('task', 'activeSession'));
    }

    public function start(Request $request)
    {
        $validated = $request->validate([
            'task_id' => ['required', 'exists:tasks,id'],
        ]);

        $task = Task::where('id', $validated['task_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $session = StudySession::create([
            'user_id' => Auth::id(),
            'task_id' => $task->id,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        if ($task->status === 'pending') {
            $task->update(['status' => 'in_progress']);
        }

        return response()->json([
            'id' => $session->id,
            'started_at' => $session->started_at->toIso8601String(),
        ]);
    }

    public function pause(StudySession $session)
    {
        $this->authorize('update', $session);

        if ($session->status === 'in_progress' && is_null($session->paused_at)) {
            $session->update(['paused_at' => now()]);
        }

        return response()->json(['paused' => true]);
    }

   public function resume(StudySession $session){
    $this->authorize('update', $session);

    if ($session->status === 'in_progress' && $session->paused_at) {
        $pausedSeconds = $session->paused_at->diffInSeconds(now());

        $session->update([
            'paused_seconds' => $session->paused_seconds + $pausedSeconds,
            'paused_at' => null,
        ]);
    }

    return response()->json([
        'resumed' => true,
        'paused_seconds' => $session->paused_seconds,
    ]);
}

    public function complete(Request $request, StudySession $session)
    {
        $this->authorize('update', $session);

        $endedAt = now();
        $duration = $this->calculateDuration($session, $endedAt);

        $session->update([
            'ended_at' => $endedAt,
            'duration_minutes' => $duration,
            'status' => 'completed',
            'paused_at' => null,
        ]);

        return response()->json(['duration_minutes' => $duration]);
    }

    public function interrupt(Request $request, StudySession $session)
    {
        $this->authorize('update', $session);

        $endedAt = now();
        $duration = $this->calculateDuration($session, $endedAt);

        $session->update([
            'ended_at' => $endedAt,
            'duration_minutes' => $duration,
            'status' => 'interrupted',
            'paused_at' => null,
        ]);

        return response()->json(['duration_minutes' => $duration]);
    }

    private function calculateDuration(StudySession $session, Carbon $endedAt): int
    {
        $pausedSeconds = $session->paused_seconds;

        if ($session->paused_at) {
            $pausedSeconds += $session->paused_at->diffInSeconds($endedAt);
        }

        $activeSeconds = $session->started_at->diffInSeconds($endedAt) - $pausedSeconds;

        return max(1, (int) round($activeSeconds / 60));
    }
}