<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_lost_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('crm_lost_reasons')->insert([
            ['name' => 'Preço', 'active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Frete', 'active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sem estoque', 'active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Concorrência', 'active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sem resposta', 'active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lost_reasons');
    }
};
