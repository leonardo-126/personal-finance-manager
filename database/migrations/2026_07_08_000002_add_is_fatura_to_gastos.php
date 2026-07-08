<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            // Marca os gastos que representam faturas de cartão importadas.
            $table->boolean('is_fatura')->default(false)->after('descricao');
        });

        // Backfill: gastos que já possuem itens são tratados como faturas.
        DB::table('gastos')
            ->whereIn('id', function ($query) {
                $query->select('gasto_id')
                    ->distinct()
                    ->from('gastos_itens')
                    ->whereNull('deleted_at');
            })
            ->update(['is_fatura' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->dropColumn('is_fatura');
        });
    }
};
