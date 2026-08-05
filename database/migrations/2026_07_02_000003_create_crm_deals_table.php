<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crm_contact_id')->constrained('crm_contacts')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('title');
            $table->decimal('value', 10, 2)->default(0);
            $table->enum('stage', ['novo_lead', 'contato_feito', 'qualificado', 'proposta', 'negociacao', 'ganho', 'perdido'])->default('novo_lead');
            $table->string('lost_reason')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamp('stage_changed_at')->nullable();
            $table->timestamps();

            $table->index(['stage', 'position']);
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_deals');
    }
};
