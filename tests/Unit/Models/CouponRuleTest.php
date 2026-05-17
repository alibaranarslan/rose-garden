<?php

namespace Tests\Unit\Models;

use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_coupon_validates_minimum_usage_limits_and_discount_types(): void
    {
        $percentage = Coupon::create([
            'code' => 'YUZDE10',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 500,
            'is_active' => true,
            'used_count' => 0,
        ]);

        $fixed = Coupon::create([
            'code' => 'SABIT75',
            'type' => 'fixed_amount',
            'value' => 75,
            'min_order_amount' => 0,
            'is_active' => true,
            'used_count' => 0,
        ]);

        $freeDelivery = Coupon::create([
            'code' => 'KARGO0',
            'type' => 'free_delivery',
            'value' => 0,
            'min_order_amount' => 0,
            'is_active' => true,
            'used_count' => 0,
        ]);

        $this->assertFalse($percentage->isValid(400));
        $this->assertTrue($percentage->isValid(600));
        $this->assertSame(60.0, $percentage->calculateDiscount(600));

        $this->assertTrue($fixed->isValid(100));
        $this->assertSame(75.0, $fixed->calculateDiscount(300));
        $this->assertSame(50.0, $fixed->calculateDiscount(50));

        $this->assertTrue($freeDelivery->isValid(100));
        $this->assertSame(0.0, $freeDelivery->calculateDiscount(100));
    }
}
