<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_the_home_dashboard_returns_a_successful_response(): void
    {
        $response = $this->get('/home');

        $response->assertStatus(200);
    }

    public function test_khmer_is_the_default_application_language(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('<html lang="km">', false);
    }
}
