<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('address_cep', 30)->nullable()->change();
            $table->string('address_number', 100)->nullable()->change();
            $table->string('address_complement', 200)->nullable()->change();
            $table->string('address_state', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('address_cep', 9)->nullable()->change();
            $table->string('address_number', 20)->nullable()->change();
            $table->string('address_complement', 100)->nullable()->change();
            $table->string('address_state', 2)->nullable()->change();
        });
    }
};
