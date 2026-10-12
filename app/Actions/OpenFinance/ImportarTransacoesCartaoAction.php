<?php

namespace App\Actions\OpenFinance;

use App\Actions\Faturas\ImportarFaturaAction;
use App\Enums\StatusTransacaoOpenFinance;
use App\Enums\TipoContaOpenFinance;
use App\Models\Gastos;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ImportarTransacoesCartaoAction
{
    public function __construct(private readonly ImportarFaturaAction $importarFatura)
    {
    }

    /**
     * Transforma transações pendentes de um cartão em uma fatura, reaproveitando
     * o mesmo fluxo da importação de arquivo do Nubank.
     *
     * @param  array{transacao_ids: list<int>, caixa_id?: ?int, categoria_id: int, descricao?: ?string, data_gasto?: ?string}  $data
     */
    public function execute(User $user, int $contaId, array $data): Gastos
    {
        $conta = $user->openFinanceContas()->findOrFail($contaId);

        if ($conta->tipo !== TipoContaOpenFinance::Cartao) {
            throw ValidationException::withMessages(['conta' => 'Só contas de cartão podem ser importadas como fatura.']);
        }

        $caixaId = $data['caixa_id'] ?? $conta->caixa_id;
        if (! $caixaId) {
            throw ValidationException::withMessages(['caixa_id' => 'Informe o caixa ou vincule um caixa à conta.']);
        }

        return DB::transaction(function () use ($conta, $data, $caixaId) {
            $transacoes = $conta->transacoes()
                ->whereIn('id', $data['transacao_ids'])
                ->where('status', StatusTransacaoOpenFinance::Pendente)
                ->orderBy('data')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($transacoes->isEmpty()) {
                throw ValidationException::withMessages(['transacao_ids' => 'Nenhuma transação pendente encontrada.']);
            }

            $gasto = $this->importarFatura->execute(
                $transacoes->map(fn ($t) => [
                    'data'      => $t->data->toDateString(),
                    'descricao' => $t->descricao,
                    'valor'     => $t->valorAssinado(),
                ])->all(),
                [
                    'caixa_id'     => $caixaId,
                    'categoria_id' => $data['categoria_id'],
                    'descricao'    => $data['descricao'] ?? "Fatura {$conta->nome}",
                    'data_gasto'   => $data['data_gasto'] ?? null,
                ],
            );

            // createMany preserva a ordem, então o i-ésimo item corresponde à i-ésima transação.
            $itens = $gasto->itens->sortBy('id')->values();
            foreach ($transacoes->values() as $i => $transacao) {
                $transacao->update([
                    'status'        => StatusTransacaoOpenFinance::Importada,
                    'gasto_item_id' => $itens[$i]->id,
                ]);
            }

            return $gasto;
        });
    }
}
