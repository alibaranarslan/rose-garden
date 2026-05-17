<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class GoogleAuthAvailabilityTest extends TestCase
{
    public function test_google_auth_redirect_is_blocked_when_google_is_not_configured(): void
    {
        config()->set('services.google.client_id', '');
        config()->set('services.google.client_secret', '');

        $response = $this->get(route('auth.google'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Google ile giriş şu anda aktif değil.');
    }
}
