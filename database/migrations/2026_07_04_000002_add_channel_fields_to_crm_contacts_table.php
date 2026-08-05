<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_contacts', function (Blueprint $table) {
            $table->string('whatsapp_number')->nullable()->after('phone');
            $table->string('instagram_user_id')->nullable()->after('whatsapp_number');
            $table->string('instagram_username')->nullable()->after('instagram_user_id');

            $table->index('whatsapp_number');
            $table->index('instagram_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('crm_contacts', function (Blueprint $table) {
            $table->dropIndex(['whatsapp_number']);
            $table->dropIndex(['instagram_user_id']);
            $table->dropColumn(['whatsapp_number', 'instagram_user_id', 'instagram_username']);
        });
    }
};
