<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginPageDesignTest extends TestCase
{
    public function test_login_page_displays_redesigned_member_access_screen(): void
    {
        $response = $this->get('/login');

        $response
            ->assertStatus(200)
            ->assertSee('login-page', false)
            ->assertSee('login-brand__logo', false)
            ->assertDontSee('Secure member access')
            ->assertDontSee('Platform sign in')
            ->assertSee('Access <span class="text-accent">Saudi CISO</span>', false)
            ->assertDontSee('Use your member credentials')
            ->assertDontSee('SaudiCISO.net member access is reserved')
            ->assertSee('action="http://localhost/login"', false)
            ->assertSee('Enter Platform');
    }
}
