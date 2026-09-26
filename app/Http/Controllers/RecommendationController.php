<?php
namespace App\Http\Controllers;

use App\Services\RecommendationService;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    public function index(RecommendationService $recommendationService)
    {
        $userId = Auth::id();

        $ranked = $recommendationService->getRankedTasks($userId, 5);

        return view('recommendation.index', [
            'ranked' => $ranked,
        ]);
    }
}