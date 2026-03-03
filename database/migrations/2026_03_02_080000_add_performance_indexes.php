<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function hasIndex(string $table, string $indexName): bool
    {
        $result = DB::select('SHOW INDEX FROM `'.$table.'` WHERE Key_name = ?', [$indexName]);

        return ! empty($result);
    }

    public function up(): void
    {
        if (! $this->hasIndex('products', 'products_created_at_idx')) {
            Schema::table('products', function (Blueprint $table) {
                $table->index('created_at', 'products_created_at_idx');
            });
        }

        if (! $this->hasIndex('products', 'products_sale_price_idx')) {
            Schema::table('products', function (Blueprint $table) {
                $table->index('sale_price', 'products_sale_price_idx');
            });
        }

        if (! $this->hasIndex('cart_items', 'cart_items_user_product_idx')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->index(['user_id', 'product_id'], 'cart_items_user_product_idx');
            });
        }

        if (! $this->hasIndex('loyalty_transactions', 'loyalty_transactions_user_created_idx')) {
            Schema::table('loyalty_transactions', function (Blueprint $table) {
                $table->index(['user_id', 'created_at'], 'loyalty_transactions_user_created_idx');
            });
        }

        if (! $this->hasIndex('blog_posts', 'blog_posts_status_published_idx')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->index(['status', 'published_at'], 'blog_posts_status_published_idx');
            });
        }

        if (! $this->hasIndex('customer_events', 'customer_events_user_month_day_idx')) {
            Schema::table('customer_events', function (Blueprint $table) {
                $table->index(['user_id', 'event_month', 'event_day'], 'customer_events_user_month_day_idx');
            });
        }

        if (! $this->hasIndex('notification_logs', 'notification_logs_user_created_idx')) {
            Schema::table('notification_logs', function (Blueprint $table) {
                $table->index(['user_id', 'created_at'], 'notification_logs_user_created_idx');
            });
        }
    }

    public function down(): void
    {
        if ($this->hasIndex('products', 'products_created_at_idx')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex('products_created_at_idx');
            });
        }

        if ($this->hasIndex('products', 'products_sale_price_idx')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex('products_sale_price_idx');
            });
        }

        if ($this->hasIndex('cart_items', 'cart_items_user_product_idx')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->dropIndex('cart_items_user_product_idx');
            });
        }

        if ($this->hasIndex('loyalty_transactions', 'loyalty_transactions_user_created_idx')) {
            Schema::table('loyalty_transactions', function (Blueprint $table) {
                $table->dropIndex('loyalty_transactions_user_created_idx');
            });
        }

        if ($this->hasIndex('blog_posts', 'blog_posts_status_published_idx')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->dropIndex('blog_posts_status_published_idx');
            });
        }

        if ($this->hasIndex('customer_events', 'customer_events_user_month_day_idx')) {
            Schema::table('customer_events', function (Blueprint $table) {
                $table->dropIndex('customer_events_user_month_day_idx');
            });
        }

        if ($this->hasIndex('notification_logs', 'notification_logs_user_created_idx')) {
            Schema::table('notification_logs', function (Blueprint $table) {
                $table->dropIndex('notification_logs_user_created_idx');
            });
        }
    }
};
