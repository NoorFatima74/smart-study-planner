<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\StudySessionController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\PlannerController;
use App\Http\Controllers\AnalyticsController;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('subjects', SubjectController::class);
    Route::resource('tasks', TaskController::class);
    Route::get('/timer', [StudySessionController::class, 'select'])->name('timer.index');
    Route::get('/tasks/{task}/timer', [StudySessionController::class, 'timer'])->name('tasks.timer');
    Route::resource('goals', GoalController::class);
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');
    Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
    Route::get('/recommendation', [RecommendationController::class, 'index'])->name('recommendation.index');
    Route::get('/planner', [PlannerController::class, 'index'])->name('planner.index');
    Route::get('/planner/missed', [PlannerController::class, 'missed'])->name('planner.missed');
    Route::post('/planner/reschedule', [PlannerController::class, 'reschedule'])->name('planner.reschedule');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/ai-planner', [App\Http\Controllers\AiPlannerController::class, 'create'])->name('ai-planner.create');
    Route::post('/ai-planner/generate', [App\Http\Controllers\AiPlannerController::class, 'generate'])->name('ai-planner.generate');
    Route::get('/ai-planner/review', [App\Http\Controllers\AiPlannerController::class, 'review'])->name('ai-planner.review');
    Route::post('/ai-planner/save', [App\Http\Controllers\AiPlannerController::class, 'save'])->name('ai-planner.save');

    Route::prefix('study-sessions')->name('study-sessions.')->group(function () {
    Route::get('/', [StudySessionController::class, 'index'])->name('index');
    Route::post('/start', [StudySessionController::class, 'start'])->name('start');
    Route::post('/{session}/pause', [StudySessionController::class, 'pause'])->name('pause');
    Route::post('/{session}/resume', [StudySessionController::class, 'resume'])->name('resume');
    Route::post('/{session}/complete', [StudySessionController::class, 'complete'])->name('complete');
    Route::post('/{session}/interrupt', [StudySessionController::class, 'interrupt'])->name('interrupt');
    
});

});

Route::get('/test-ai', function (\App\Services\AiService $ai) {
    $result = $ai->generate('Reply with JSON: {"status": "ok", "message": "Gemini is connected"}');
    return $result;
});
require __DIR__.'/auth.php';