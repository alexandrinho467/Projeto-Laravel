<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_deal_stage_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crm_deal_id')->constrained('crm_deals')->cascadeOnDelete();
            $table->string('stage', 30);
            $table->timestamp('entered_at');

            $table->index(['crm_deal_id', 'entered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_deal_stage_history');
    }
};
