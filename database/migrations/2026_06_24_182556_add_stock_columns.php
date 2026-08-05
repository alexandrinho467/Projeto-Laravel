<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->unsignedInteger('stock')->default(0)->after('available');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('stock_decremented')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('stock_decremented');
        });
    }
};
