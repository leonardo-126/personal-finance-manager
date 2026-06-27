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
            // Data original da transação (usada na importação de faturas de cartão).
            $table->date('data_transacao')->nullable()->after('motivo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gastos_itens', function (Blueprint $table) {
            $table->dropColumn('data_transacao');
        });
    }
};
