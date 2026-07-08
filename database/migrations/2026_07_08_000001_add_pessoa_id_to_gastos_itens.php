<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gastos_itens', function (Blueprint $table) {
            // Pessoa que fez a transação (quem usou o cartão). Opcional.
            $table->foreignId('pessoa_id')
                ->nullable()
                ->after('data_transacao')
                ->constrained('pessoas')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gastos_itens', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pessoa_id');
        });
    }
};
