<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Uma conexão = um consentimento do usuário em uma instituição (o "item" do agregador).
        Schema::create('open_finance_conexoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider', 32);
            $table->string('external_id');
            $table->string('instituicao_id')->nullable();
            $table->string('instituicao_nome')->nullable();
            $table->string('instituicao_logo')->nullable();
            $table->enum('status', ['atualizando', 'ativa', 'erro_login', 'desatualizada', 'aguardando_usuario'])
                ->default('atualizando');
            $table->text('erro')->nullable();
            $table->timestamp('consentimento_expira_em')->nullable();
            $table->timestamp('ultimo_sync_em')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'external_id']);
        });

        Schema::create('open_finance_contas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conexao_id')->constrained('open_finance_conexoes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Caixa onde as transações desta conta devem cair ao importar. Opcional.
            $table->foreignId('caixa_id')->nullable()->constrained('caixas_financeiras')->nullOnDelete();
            $table->string('external_id');
            $table->enum('tipo', ['corrente', 'poupanca', 'cartao', 'outra']);
            $table->string('nome');
            // Número da conta/cartão: guardado criptografado (cast `encrypted`).
            $table->text('numero')->nullable();
            $table->decimal('saldo', 15, 2)->nullable();
            $table->string('moeda', 3)->default('BRL');
            $table->timestamps();

            $table->unique(['conexao_id', 'external_id']);
        });

        // Área de staging: tudo que vem do banco cai aqui primeiro e o usuário
        // decide o que vira gasto. O par (conta_id, external_id) garante idempotência.
        Schema::create('open_finance_transacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conta_id')->constrained('open_finance_contas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('external_id');
            $table->date('data');
            $table->string('descricao');
            // Sempre positivo; o sentido vem de `tipo`.
            $table->decimal('valor', 15, 2);
            $table->enum('tipo', ['debito', 'credito']);
            $table->string('categoria_externa')->nullable();
            // Situação no banco: transações "pending" ainda podem mudar ou sumir.
            $table->boolean('confirmada')->default(true);
            $table->enum('status', ['pendente', 'importada', 'ignorada'])->default('pendente');
            $table->foreignId('gasto_item_id')->nullable()->constrained('gastos_itens')->nullOnDelete();
            $table->timestamps();

            $table->unique(['conta_id', 'external_id']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('open_finance_transacoes');
        Schema::dropIfExists('open_finance_contas');
        Schema::dropIfExists('open_finance_conexoes');
    }
};
