<?php
namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            ['name' => 'First Step', 'description' => 'Complete your first task', 'icon' => '🎯', 'type' => 'tasks_completed', 'threshold' => 1],
            ['name' => 'Getting Started', 'description' => 'Complete 10 tasks', 'icon' => '📘', 'type' => 'tasks_completed', 'threshold' => 10],
            ['name' => 'Task Master', 'description' => 'Complete 50 tasks', 'icon' => '🏆', 'type' => 'tasks_completed', 'threshold' => 50],
            ['name' => 'Century Club', 'description' => 'Complete 100 tasks', 'icon' => '💯', 'type' => 'tasks_completed', 'threshold' => 100],
            ['name' => 'Week Warrior', 'description' => 'Maintain a 7-day streak', 'icon' => '🔥', 'type' => 'streak', 'threshold' => 7],
            ['name' => 'Unstoppable', 'description' => 'Maintain a 30-day streak', 'icon' => '⚡', 'type' => 'streak', 'threshold' => 30],
            ['name' => 'Rising Star', 'description' => 'Reach Level 5', 'icon' => '⭐', 'type' => 'level', 'threshold' => 5],
            ['name' => 'Veteran', 'description' => 'Reach Level 10', 'icon' => '👑', 'type' => 'level', 'threshold' => 10],
        ];

        foreach ($achievements as $achievement) {
            Achievement::updateOrCreate(
                ['type' => $achievement['type'], 'threshold' => $achievement['threshold']],
                $achievement
            );
        }
    }
}