<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicRedesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_public_pages_render_via_inertia_react_without_legacy_slop(): void
    {
        $routes = [
            '/'         => 'Public/Home',
            '/about'    => 'Public/About',
            '/features' => 'Public/Features',
            '/contact'  => 'Public/Contact',
            '/docs'     => 'Public/Docs',
        ];

        foreach ($routes as $uri => $component) {
            $response = $this->get($uri);

            $response->assertOk();
            $response->assertInertia(fn (Assert $page) => $page->component($component));
            $response->assertDontSee('mailto:admin@visioncode.ai', false);
            $response->assertDontSee('#B026FF', false);
            $response->assertDontSee('#00F3FF', false);
            $response->assertDontSee('aurora-text', false);
            $response->assertDontSee('bg-[#040d21]', false);
            $response->assertDontSee('href="' . url('/register') . '"', false);
        }
    }

    public function test_homepage_renders_public_home_inertia_component_with_shared_props(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/Home')
                ->has('auth')
                ->has('flash')
            );
    }

    public function test_docs_page_renders_public_docs_component_without_threejs_bundle_tag(): void
    {
        $response = $this->get('/docs');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page->component('Public/Docs'));
        $response->assertDontSee('hero-3d.js', false);
        $response->assertDontSee('three.module.js', false);
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $response = $this->from('/contact')->post('/contact', [
            'name'        => '',
            'email'       => 'not-an-email',
            'institution' => '',
            'message'     => 'short',
        ]);

        $response->assertRedirect('/contact');
        $response->assertSessionHasErrors(['name', 'email', 'institution', 'message']);
    }

    public function test_contact_form_submits_successfully_and_flashes_status(): void
    {
        Mail::fake();
        Log::spy();

        $response = $this->from('/contact')->post('/contact', [
            'name'        => 'Dr. Nadia Rahman',
            'email'       => 'n.rahman@university.edu',
            'institution' => 'National Institute of Technology',
            'role'        => 'faculty',
            'cohort_size' => '100_500',
            'message'     => 'We would like to evaluate VisionLab for our CS-301 Systems course this fall.',
        ]);

        $response->assertRedirect('/contact');
        $response->assertSessionHas('status');
    }

    public function test_contact_form_enforces_throttle_rate_limit(): void
    {
        Mail::fake();

        $payload = [
            'name'        => 'Rate Limit Probe',
            'email'       => 'probe@university.edu',
            'institution' => 'Test University',
            'role'        => 'it_admin',
            'cohort_size' => 'under_100',
            'message'     => 'Testing campus pilot endpoint throttle protection.',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->post('/contact', $payload)->assertRedirect('/contact');
        }

        $this->post('/contact', $payload)->assertStatus(429);
    }
}
