<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $tables = [
        'users', 'products', 'product_images', 'product_sizes', 'product_features',
        'orders', 'order_items', 'coupons', 'site_settings', 'password_reset_tokens',
        'sessions', 'stock_movements', 'reviews',
        'crm_contacts', 'crm_deals', 'crm_activities',
    ];

    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') return;

        foreach ($this->tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ALTER TABLE `{$table}` ENGINE=InnoDB");
            }
        }

        Schema::table('product_images', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::table('product_sizes', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::table('product_features', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('crm_contacts', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('crm_deals', function (Blueprint $table) {
            $table->foreign('crm_contact_id')->references('id')->on('crm_contacts')->cascadeOnDelete();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();
        });

        Schema::table('crm_activities', function (Blueprint $table) {
            $table->foreign('crm_contact_id')->references('id')->on('crm_contacts')->cascadeOnDelete();
            $table->foreign('crm_deal_id')->references('id')->on('crm_deals')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') return;

        Schema::table('product_images', fn (Blueprint $table) => $table->dropForeign(['product_id']));
        Schema::table('product_sizes', fn (Blueprint $table) => $table->dropForeign(['product_id']));
        Schema::table('product_features', fn (Blueprint $table) => $table->dropForeign(['product_id']));
        Schema::table('orders', fn (Blueprint $table) => $table->dropForeign(['user_id']));
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['product_id']);
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::table('crm_contacts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['assigned_to']);
        });
        Schema::table('crm_deals', function (Blueprint $table) {
            $table->dropForeign(['crm_contact_id']);
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['order_id']);
        });
        Schema::table('crm_activities', function (Blueprint $table) {
            $table->dropForeign(['crm_contact_id']);
            $table->dropForeign(['crm_deal_id']);
            $table->dropForeign(['user_id']);
        });
    }
};
