<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('abandoned_carts', function (Blueprint $table) {
            if (! Schema::hasColumn('abandoned_carts', 'last_reminder_status')) {
                $table->string('last_reminder_status', 32)->nullable()->after('last_reminded_at');
            }

            if (! Schema::hasColumn('abandoned_carts', 'last_reminder_error')) {
                $table->text('last_reminder_error')->nullable()->after('last_reminder_status');
            }

            if (! Schema::hasColumn('abandoned_carts', 'last_reminder_channel')) {
                $table->string('last_reminder_channel', 16)->nullable()->after('last_reminder_error');
            }

            if (! Schema::hasColumn('abandoned_carts', 'last_reminder_attempted_at')) {
                $table->timestamp('last_reminder_attempted_at')->nullable()->after('last_reminder_channel');
            }
        });
    }

    public function down(): void
    {
        Schema::table('abandoned_carts', function (Blueprint $table) {
            foreach ([
                'last_reminder_attempted_at',
                'last_reminder_channel',
                'last_reminder_error',
                'last_reminder_status',
            ] as $column) {
                if (Schema::hasColumn('abandoned_carts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
