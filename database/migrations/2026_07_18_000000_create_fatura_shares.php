<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Compartilhamento de fatura: um token de acesso por par (fatura, pessoa).
     * Cada pessoa recebe um link próprio para marcar os itens que são dela,
     * sem precisar de conta.
     */
    public function up(): void
    {
        Schema::create('fatura_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gasto_id')->constrained('gastos')->cascadeOnDelete();
            $table->foreignId('pessoa_id')->constrained('pessoas')->cascadeOnDelete();
            // Token opaco usado na URL pública (/fatura-compartilhada/{token}).
            $table->string('token', 64)->unique();
            $table->timestamps();

            // No máximo um link por pessoa em cada fatura.
            $table->unique(['gasto_id', 'pessoa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fatura_shares');
    }
};
