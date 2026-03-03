<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_occasion', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('special_occasion_id')->constrained('special_occasions')->cascadeOnDelete();
            $table->primary(['product_id', 'special_occasion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_occasion');
    }
};
