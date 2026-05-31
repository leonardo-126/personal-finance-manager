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
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('caixa_id')->constrained('caixas_financeiras')->cascadeOnDelete();
            $table->foreignId('categoria_id')->constrained('categorias_gastos')->cascadeOnDelete();
            $table->decimal('valor_total', 15, 2);
            $table->text('descricao')->nullable();
            $table->dateTime('data_gasto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
