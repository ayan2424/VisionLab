<?php

namespace Database\Factories;

use App\Models\Extension;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ExtensionFactory extends Factory
{
    protected $model = Extension::class;

    public function definition(): array
    {
        return [
            'name'               => fake()->words(2, true),
            'package_identifier' => fake()->unique()->slug(3) . '.' . fake()->slug(2),
            'version'            => fake()->semver(),
            'description'        => fake()->sentence(),
            'category'           => fake()->randomElement(['utility', 'language', 'theme', 'linter', 'ai', 'collaboration']),
            'artifact_path'      => null,
            'checksum'           => null,
            'source'             => null,
            'is_global'          => false,
            'is_builtin'         => false,
            'is_required'        => false,
            'is_active'          => true,
            'rollout_state'      => 'released',
        ];
    }

    public function required(): static
    {
        return $this->state(fn () => ['is_required' => true, 'is_builtin' => true]);
    }

    public function builtin(): static
    {
        return $this->state(fn () => ['is_builtin' => true]);
    }

    public function withArtifact(): static
    {
        return $this->state(fn () => [
            'artifact_path' => 'extensions/' . Str::random(8) . '.vsix',
            'checksum'      => hash('sha256', Str::random(64)),
        ]);
    }
}
