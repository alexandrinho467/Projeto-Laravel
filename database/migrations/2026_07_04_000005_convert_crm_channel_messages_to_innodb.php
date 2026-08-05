<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A criação da tabela (migration anterior) já define engine InnoDB e as
        // foreign keys via ->constrained(). Esta migration só corrige instalações
        // antigas em que a tabela ainda estava em MyISAM sem as FKs aplicadas.
        $engine = DB::selectOne("SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'crm_channel_messages'");

        if (($engine->ENGINE ?? null) === 'InnoDB') {
            return;
        }

        // Remove linhas órfãs criadas enquanto a tabela estava em MyISAM
        // (sem FK aplicada, então cascadeOnDelete de contatos removidos em testes não teve efeito).
        DB::statement('
            DELETE m FROM crm_channel_messages m
            LEFT JOIN crm_contacts c ON c.id = m.crm_contact_id
            WHERE c.id IS NULL
        ');

        DB::statement('ALTER TABLE crm_channel_messages ENGINE=InnoDB');

        Schema::table('crm_channel_messages', function (Blueprint $table) {
            $table->foreign('crm_contact_id')->references('id')->on('crm_contacts')->cascadeOnDelete();
            $table->foreign('crm_deal_id')->references('id')->on('crm_deals')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        //
    }
};
