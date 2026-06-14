<?php

namespace Database\Factories;

use App\Models\AnalyticsEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AnalyticsEventFactory extends Factory
{
    protected $model = AnalyticsEvent::class;

    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'event_type'     => fake()->randomElement([
                'workspace.started', 'workspace.stopped', 'file.saved',
                'assignment.submitted', 'ai.chat.sent', 'ai.patch.approved',
                'course.enrolled', 'video.joined', 'login', 'page.viewed',
            ]),
            'event_data'     => ['source' => 'test'],
            'resource_type'  => null,
            'resource_id'    => null,
            'ip_address'     => fake()->ipv4(),
            'user_agent'     => fake()->userAgent(),
            'correlation_id' => Str::uuid()->toString(),
        ];
    }
}
