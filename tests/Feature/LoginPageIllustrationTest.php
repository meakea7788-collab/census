<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginPageIllustrationTest extends TestCase
{
    public function test_login_page_displays_the_census_illustration(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Census community illustration')
            ->assertSee('<svg', false)
            ->assertSee('max-w-6xl', false);
    }
}
