<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Extension;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExtensionRegistryTest extends TestCase
{
    use RefreshDatabase;

    public function test_extension_checksum_verification_endpoint()
    {
        $user = User::factory()->create();
        
        $extension = Extension::factory()->create([
            'name' => 'VisionLab Agent',
            'package_identifier' => 'visionlab.agent',
            'version' => '1.0.0',
            'checksum' => 'fakehash123'
        ]);

        $response = $this->actingAs($user)->getJson('/api/extensions');

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'identifier' => 'visionlab.agent',
                     'sha256_checksum' => 'fakehash123'
                 ]);
    }
}
