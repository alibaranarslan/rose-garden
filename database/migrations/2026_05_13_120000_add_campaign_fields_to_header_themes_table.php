<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('header_themes', function (Blueprint $table): void {
            $table->json('headline')->nullable()->after('banner_message');
            $table->json('subline')->nullable()->after('headline');
            $table->json('cta_label')->nullable()->after('subline');
            $table->string('special_occasion_slug')->nullable()->after('cta_label');
            $table->string('cta_url')->nullable()->after('special_occasion_slug');
            $table->string('campaign_image')->nullable()->after('cta_url');
        });
    }

    public function down(): void
    {
        Schema::table('header_themes', function (Blueprint $table): void {
            $table->dropColumn([
                'headline',
                'subline',
                'cta_label',
                'special_occasion_slug',
                'cta_url',
                'campaign_image',
            ]);
        });
    }
};
