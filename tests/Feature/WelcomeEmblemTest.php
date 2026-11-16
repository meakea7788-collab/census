<?php

namespace Tests\Feature;

use Tests\TestCase;

class WelcomeEmblemTest extends TestCase
{
    public function test_welcome_page_displays_the_cambodia_emblem_in_the_circle(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(asset('images/cambodia-national-emblem.png'), false)
            ->assertSee('alt="Cambodia national emblem"', false);

        $this->assertFileExists(public_path('images/cambodia-national-emblem.png'));
    }
}
