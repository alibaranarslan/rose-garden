<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetThrottleTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_reset_request_route_is_rate_limited(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->post(route('password.email'), [
                'email' => "missing{$i}@example.com",
            ])->assertStatus(302);
        }

        $this->post(route('password.email'), [
            'email' => 'missing4@example.com',
        ])->assertStatus(429);
    }

    public function test_password_reset_email_link_preserves_user_locale_prefix(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'reset-locale@example.com',
            'preferred_language' => 'tr',
        ]);

        $this->post('/tr/sifremi-unuttum', [
            'email' => $user->email,
        ])->assertRedirect();

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
            $mail = $notification->toMail($user);

            return str_contains($mail->actionUrl, '/tr/sifre-sifirla/')
                && str_contains($mail->actionUrl, 'email=reset-locale%40example.com');
        });
    }
}
