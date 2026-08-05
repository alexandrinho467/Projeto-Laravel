<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_stage_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->string('stage', 30);
            $table->string('key', 60);
            $table->string('label');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['stage', 'key']);
            $table->index('stage');
        });

        $now = now();
        DB::table('crm_stage_checklist_items')->insert([
            ['stage' => 'proposta', 'key' => 'tamanho', 'label' => 'Confirmar tamanho US', 'position' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['stage' => 'proposta', 'key' => 'frete', 'label' => 'Enviar valor do frete', 'position' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['stage' => 'negociacao', 'key' => 'pagamento', 'label' => 'Confirmar forma de pagamento', 'position' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['stage' => 'negociacao', 'key' => 'endereco', 'label' => 'Confirmar endereço de entrega', 'position' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_stage_checklist_items');
    }
};
