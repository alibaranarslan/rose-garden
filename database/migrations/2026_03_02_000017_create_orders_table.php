<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'awaiting_payment', 'paid', 'preparing', 'on_the_way', 'delivered', 'cancelled', 'refunded'])->default('pending');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 8, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('loyalty_points_used', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);
            $table->enum('payment_method', ['credit_card', 'bank_transfer']);
            $table->string('sender_name', 255);
            $table->string('sender_phone', 20);
            $table->string('sender_email', 255);
            $table->string('recipient_name', 255);
            $table->string('recipient_phone', 20);
            $table->text('recipient_address');
            $table->string('recipient_district', 100)->nullable();
            $table->foreignId('delivery_zone_id')->nullable()->constrained('delivery_zones')->nullOnDelete();
            $table->date('delivery_date');
            $table->foreignId('delivery_time_slot_id')->nullable()->constrained('delivery_time_slots')->nullOnDelete();
            $table->text('delivery_note')->nullable();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->text('admin_note')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('payment_method');
            $table->index('delivery_date');
            $table->index('created_at');
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
