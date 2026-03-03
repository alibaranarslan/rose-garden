<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keyword_dictionary', function (Blueprint $table) {
            $table->id();
            $table->string('keyword', 255);
            $table->enum('event_type', ['birthday', 'anniversary', 'valentines', 'mothers_day', 'custom']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('keyword');
            $table->index('event_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keyword_dictionary');
    }
};
