<?php

namespace Tests\Feature\Auth;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthCartMergeTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_merges_guest_cart_items(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret1234'),
        ]);

        $product = Product::create([
            'name' => ['tr' => 'Saksida Cicek'],
            'slug' => 'saksida-cicek',
            'price' => 200,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        CartItem::create([
            'session_id' => 'guest-login-session',
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->withSession(['cart_session_id' => 'guest-login-session'])
            ->post(route('login.submit'), [
                'email' => $user->email,
                'password' => 'secret1234',
            ])
            ->assertRedirect(route('account.dashboard'));

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }

    public function test_register_merges_guest_cart_items(): void
    {
        $product = Product::create([
            'name' => ['tr' => 'Pembe Lale'],
            'slug' => 'pembe-lale',
            'price' => 180,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        CartItem::create([
            'session_id' => 'guest-register-session',
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->withSession(['cart_session_id' => 'guest-register-session'])
            ->post(route('register.submit'), [
                'name' => 'Yeni Kullanici',
                'email' => 'yeni@example.com',
                'phone' => '05000000000',
                'password' => 'secret1234',
                'password_confirmation' => 'secret1234',
                'kvkk_acknowledged' => '1',
            ])
            ->assertRedirect(route('account.dashboard'));

        $user = User::where('email', 'yeni@example.com')->firstOrFail();

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_register_shows_kvkk_validation_error(): void
    {
        app()->setLocale('tr');

        $this->from('/tr/kayit')
            ->followingRedirects()
            ->post('/tr/kayit', [
                'name' => 'KVKK Eksik',
                'email' => 'kvkk-eksik@example.com',
                'phone' => '05000000000',
                'password' => 'secret1234',
                'password_confirmation' => 'secret1234',
            ])
            ->assertOk()
            ->assertSee(__('auth.kvkk_required'));

        $this->assertDatabaseMissing('users', [
            'email' => 'kvkk-eksik@example.com',
        ]);
    }
}
