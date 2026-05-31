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
        Schema::create('movimentacoes_caixas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caixa_id')->constrained('caixas_financeiras')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('renda_id')->nullable()->constrained('rendas')->nullOnDelete();
            $table->decimal('valor', 15, 2);
            $table->enum('tipo', ['entrada', 'saida', 'transferencia']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimentacoes_caixas');
    }
};
