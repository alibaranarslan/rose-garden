<?php

namespace Tests\Feature\Storefront;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAbuseProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_route_is_rate_limited(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->post(route('login.submit'), [
                'email' => 'missing@example.com',
                'password' => 'wrong-password',
            ])->assertStatus(302);
        }

        $this->post(route('login.submit'), [
            'email' => 'missing@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(429);
    }

    public function test_contact_form_is_rate_limited(): void
    {
        $payload = [
            'name' => 'Musteri Test',
            'email' => 'musteri@example.com',
            'subject' => 'Bilgi',
            'message' => 'Teslimat hakkinda bilgi almak istiyorum.',
        ];

        for ($i = 1; $i <= 3; $i++) {
            $this->post(route('contact.submit'), $payload)->assertStatus(302);
        }

        $this->post(route('contact.submit'), $payload)->assertStatus(429);
    }

    public function test_search_route_is_rate_limited(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $this->get(route('search', ['q' => 'gul']))->assertOk();
        }

        $this->get(route('search', ['q' => 'gul']))->assertStatus(429);
    }
}
