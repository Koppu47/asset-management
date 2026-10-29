<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test public catalog returns a successful response.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/assets');

        $response->assertStatus(200);
    }

    /**
     * Test root url redirects to assets catalog.
     */
    public function test_root_redirects_to_assets(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/assets');
    }
}
