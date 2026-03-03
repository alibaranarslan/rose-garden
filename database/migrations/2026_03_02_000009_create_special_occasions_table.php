<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('special_occasions', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug', 255)->unique();
            $table->unsignedTinyInteger('date_month');
            $table->unsignedTinyInteger('date_day');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->decimal('loyalty_multiplier', 3, 1)->default(1.0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('special_occasions');
    }
};
