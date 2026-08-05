<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') return;

        DB::statement("ALTER TABLE users MODIFY role ENUM('customer','admin','vendedor') DEFAULT 'customer'");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') return;

        DB::statement("UPDATE users SET role = 'customer' WHERE role = 'vendedor'");
        DB::statement("ALTER TABLE users MODIFY role ENUM('customer','admin') DEFAULT 'customer'");
    }
};
