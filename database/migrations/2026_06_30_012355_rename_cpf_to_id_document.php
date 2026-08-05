<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('cpf', 'id_document');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('id_document', 30)->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('guest_cpf', 'guest_id_document');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('guest_id_document', 30)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('id_document', 'cpf');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('cpf', 14)->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('guest_id_document', 'guest_cpf');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('guest_cpf', 14)->nullable()->change();
        });
    }
};
