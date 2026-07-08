<?php

namespace App\Actions\Faturas;

use App\Models\Gastos;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImportarFaturaAction
{
    /**
     * Cria o gasto que representa a fatura e um item por transação importada.
     *
     * @param  array<int, array{data: ?string, descricao: string, valor: float}>  $transacoes
     * @param  array{caixa_id: int, categoria_id: int, descricao?: ?string, data_gasto?: ?string}  $data
     */
    public function execute(array $transacoes, array $data): Gastos
    {
        return DB::transaction(function () use ($transacoes, $data) {
            $valorTotal = array_sum(array_column($transacoes, 'valor'));

            // Usa a data informada ou a maior data entre as transações; cai para hoje.
            $dataGasto = $data['data_gasto']
                ?? collect($transacoes)->pluck('data')->filter()->max()
                ?? Carbon::now()->toDateString();

            $gasto = Auth::user()->gastos()->create([
                'caixa_id'     => $data['caixa_id'],
                'categoria_id' => $data['categoria_id'],
                'valor_total'  => $valorTotal,
                'descricao'    => $data['descricao'] ?? 'Fatura importada do Nubank',
                'is_fatura'    => true,
                'data_gasto'   => $dataGasto,
            ]);

            $itens = array_map(static fn (array $transacao): array => [
                'nome'           => $transacao['descricao'],
                'valor'          => $transacao['valor'],
                'motivo'         => null,
                'data_transacao' => $transacao['data'],
            ], $transacoes);

            $gasto->itens()->createMany($itens);

            return $gasto->load('itens');
        });
    }
}
