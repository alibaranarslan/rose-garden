<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('session_id', 100)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 20)->nullable();
            $table->json('cart_data');
            $table->decimal('total_value', 10, 2);
            $table->integer('reminder_count')->default(0);
            $table->timestamp('last_reminded_at')->nullable();
            $table->boolean('recovered')->default(false);
            $table->foreignId('recovered_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->timestamp('abandoned_at')->useCurrent();
            $table->timestamps();

            $table->index('user_id');
            $table->index('email');
            $table->index('recovered');
            $table->index('abandoned_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abandoned_carts');
    }
};
