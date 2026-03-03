<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('event_type', ['birthday', 'anniversary', 'valentines', 'mothers_day', 'custom']);
            $table->string('event_label', 255)->nullable();
            $table->string('recipient_name', 255)->nullable();
            $table->text('recipient_address')->nullable();
            $table->unsignedTinyInteger('event_month');
            $table->unsignedTinyInteger('event_day');
            $table->enum('detected_from', ['card_message', 'order_date', 'manual']);
            $table->foreignId('source_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->integer('reminder_days_before')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_reminded_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('event_type');
            $table->index(['event_month', 'event_day']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_events');
    }
};
