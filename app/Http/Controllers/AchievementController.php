<?php
namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Support\Facades\Auth;

class AchievementController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $earnedIds = $user->achievements()->pluck('achievements.id')->toArray();

        $achievements = Achievement::orderBy('type')->orderBy('threshold')->get()
            ->map(function ($achievement) use ($earnedIds) {
                $achievement->earned = in_array($achievement->id, $earnedIds);
                return $achievement;
            });

        return view('achievements.index', [
            'achievements' => $achievements,
            'user' => $user,
        ]);
    }
}