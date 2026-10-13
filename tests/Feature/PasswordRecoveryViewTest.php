<?php

namespace Tests\Feature;

use Tests\TestCase;

class PasswordRecoveryViewTest extends TestCase
{
    public function testGuestCanRequestAPasswordResetLink()
    {
        $this->get('/password/reset')->assertOk()
            ->assertSee('action="' . route('password.email') . '"', false)
            ->assertSee('name="email"', false)
            ->assertDontSee('customer-search-form')
            ->assertDontSee('logout-form');
        $this->assertGuest();
    }

    public function testGuestCanOpenTheNewPasswordForm()
    {
        $this->get('/password/reset/test-token?email=ana%40example.com')->assertOk()
            ->assertSee('action="' . route('password.update') . '"', false)
            ->assertSee('value="test-token"', false)
            ->assertSee('value="ana@example.com"', false)
            ->assertSee('name="password_confirmation"', false)
            ->assertDontSee('customer-search-form')
            ->assertDontSee('logout-form');
        $this->assertGuest();
    }
}
