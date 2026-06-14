<?php

namespace Database\Factories;

use App\Models\UserBadge;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserBadgeFactory extends Factory
{
    protected $model = UserBadge::class;

    public function definition(): array
    {
        $badges = [
            ['badge_type' => 'first_submission',  'name' => 'First Submission',  'icon' => '📝', 'description' => 'Submitted your first assignment'],
            ['badge_type' => 'streak_7',          'name' => '7-Day Streak',      'icon' => '🔥', 'description' => 'Maintained a 7-day activity streak'],
            ['badge_type' => 'streak_30',         'name' => '30-Day Streak',     'icon' => '💎', 'description' => 'Maintained a 30-day activity streak'],
            ['badge_type' => 'ai_first_patch',    'name' => 'AI Pioneer',        'icon' => '🤖', 'description' => 'Approved your first AI patch'],
            ['badge_type' => 'perfect_score',     'name' => 'Perfect Score',     'icon' => '⭐', 'description' => 'Received 100% on an assignment'],
            ['badge_type' => 'first_deploy',      'name' => 'Ship It!',          'icon' => '🚀', 'description' => 'Deployed your first project'],
            ['badge_type' => 'collaborator',      'name' => 'Team Player',       'icon' => '🤝', 'description' => 'Collaborated on a workspace'],
        ];

        $badge = fake()->randomElement($badges);

        return [
            'user_id'      => User::factory(),
            'badge_type'   => $badge['badge_type'],
            'name'         => $badge['name'],
            'description'  => $badge['description'],
            'icon'         => $badge['icon'],
            'earned_at'    => now()->subDays(fake()->numberBetween(0, 30)),
            'source_event' => 'factory_generated',
        ];
    }
}
