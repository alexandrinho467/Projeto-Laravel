<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->enum('source', ['manual', 'site', 'instagram', 'whatsapp', 'indicacao', 'outro'])->default('manual');
            $table->enum('status', ['lead', 'ativo', 'inativo'])->default('lead');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('assigned_to');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_contacts');
    }
};
