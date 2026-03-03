<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('avatar', 255)->nullable()->after('phone');
            $table->string('google_id', 255)->nullable()->unique()->after('avatar');
            $table->boolean('is_admin')->default(false)->after('google_id');
            $table->timestamp('kvkk_accepted_at')->nullable()->after('is_admin');
            $table->boolean('marketing_consent')->default(false)->after('kvkk_accepted_at');
            $table->timestamp('marketing_consent_at')->nullable()->after('marketing_consent');
            $table->string('preferred_language', 5)->default('tr')->after('marketing_consent_at');
            $table->boolean('is_active')->default(true)->after('preferred_language');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->softDeletes()->after('last_login_at');
            $table->index('phone');
            $table->index('is_admin');
            $table->index('marketing_consent');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropIndex(['is_admin']);
            $table->dropIndex(['marketing_consent']);
            $table->dropUnique('users_google_id_unique');
            $table->dropColumn([
                'phone',
                'avatar',
                'google_id',
                'is_admin',
                'kvkk_accepted_at',
                'marketing_consent',
                'marketing_consent_at',
                'preferred_language',
                'is_active',
                'last_login_at',
                'deleted_at',
            ]);
        });
    }
};
