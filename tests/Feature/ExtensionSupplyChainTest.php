<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExtensionSupplyChainTest extends TestCase
{
    use RefreshDatabase;

    public function test_extension_integrity_exception_on_mismatch(): void
    {
        \Illuminate\Support\Facades\Event::fake([\App\Events\ExtensionIntegrityFailed::class]);

        $user = \App\Models\User::factory()->create();
        $workspace = \App\Models\Workspace::create([
            'student_id' => $user->id,
            'name' => 'test-ws',
            'status' => 'pending'
        ]);

        // Create a dummy vsix file
        $extensionsPath = storage_path('app/extensions');
        if (!is_dir($extensionsPath)) {
            mkdir($extensionsPath, 0755, true);
        }
        $vsixFile = $extensionsPath . '/test-agent.vsix';
        file_put_contents($vsixFile, 'dummy payload');

        $manager = new \App\Services\CodeServerManager();

        $this->expectException(\App\Exceptions\ExtensionIntegrityException::class);

        // Expected checksum is wrong, actual is hash('dummy payload')
        $manager->installExtension($workspace, 'test-agent.vsix', 'wrong-checksum-hash');

        // Clean up
        @unlink($vsixFile);
    }

    public function test_integrity_event_is_dispatched(): void
    {
        \Illuminate\Support\Facades\Event::fake([\App\Events\ExtensionIntegrityFailed::class]);

        $user = \App\Models\User::factory()->create();
        $workspace = \App\Models\Workspace::create([
            'student_id' => $user->id,
            'name' => 'test-ws',
            'status' => 'pending'
        ]);

        $extensionsPath = storage_path('app/extensions');
        if (!is_dir($extensionsPath)) {
            mkdir($extensionsPath, 0755, true);
        }
        $vsixFile = $extensionsPath . '/test-agent-2.vsix';
        file_put_contents($vsixFile, 'dummy payload');

        $manager = new \App\Services\CodeServerManager();

        try {
            $manager->installExtension($workspace, 'test-agent-2.vsix', 'wrong-checksum-hash');
        } catch (\App\Exceptions\ExtensionIntegrityException $e) {
            // Expected
        }

        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\ExtensionIntegrityFailed::class);

        // Clean up
        @unlink($vsixFile);
    }
}
