<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class WorkspaceTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_can_open_workspace_and_toggle_ai_panel()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@visionlab.com',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/workspace')
                    ->assertSee('VisionLab')
                    ->assertSee('Loading VS Code…')
                    // Open AI Agent Panel
                    ->click('#ai-toggle')
                    ->pause(1000)
                    ->assertSee('AI Agent')
                    ->assertSee('Gemini 2.0 Flash / Claude 3')
                    // Verify Memory Badge is visible
                    ->assertSee('MEMORY')
                    // Verify the chat tabs exist
                    ->assertSee('CHAT')
                    ->assertSee('PLAN')
                    ->assertSee('AGENT');
        });
    }
}
