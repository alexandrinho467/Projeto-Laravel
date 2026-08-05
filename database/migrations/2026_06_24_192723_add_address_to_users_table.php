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
        Schema::table('users', function (Blueprint $table) {
            $table->string('address_cep', 9)->nullable()->after('birth_date');
            $table->string('address_street')->nullable()->after('address_cep');
            $table->string('address_number', 20)->nullable()->after('address_street');
            $table->string('address_complement', 100)->nullable()->after('address_number');
            $table->string('address_neighborhood', 100)->nullable()->after('address_complement');
            $table->string('address_city', 100)->nullable()->after('address_neighborhood');
            $table->char('address_state', 2)->nullable()->after('address_city');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['address_cep','address_street','address_number','address_complement','address_neighborhood','address_city','address_state']);
        });
    }
};
