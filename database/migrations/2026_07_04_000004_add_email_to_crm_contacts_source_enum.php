<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE crm_contacts MODIFY source ENUM('manual','site','instagram','whatsapp','email','indicacao','outro') NOT NULL DEFAULT 'manual'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE crm_contacts MODIFY source ENUM('manual','site','instagram','whatsapp','indicacao','outro') NOT NULL DEFAULT 'manual'");
    }
};
