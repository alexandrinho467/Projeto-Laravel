<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_channel_messages', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('external_message_id')->nullable();
            $table->foreignId('crm_contact_id')->constrained('crm_contacts')->cascadeOnDelete();
            $table->foreignId('crm_deal_id')->nullable()->constrained('crm_deals')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('channel', ['whatsapp', 'instagram', 'email']);
            $table->enum('direction', ['enviada', 'recebida']);
            $table->string('subject')->nullable();
            $table->text('content');
            $table->timestamp('occurred_at');
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->index('crm_contact_id');
            $table->index('channel');
            $table->index('user_id');
            $table->unique(['channel', 'external_message_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_channel_messages');
    }
};
