<?php

namespace App\Actions\MovimentacoesCaixas;

use App\Models\MovimentacoesCaixas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateMovimentacaoCaixaAction
{
    public function execute(int $id, array $data): MovimentacoesCaixas
    {
        return DB::transaction(function () use ($id, $data) {
            $movimentacao = Auth::user()->movimentacoesCaixas()->findOrFail($id);

            $movimentacao->update([
                'caixa_id' => $data['caixa_id'],
                'renda_id' => $data['renda_id'] ?? null,
                'valor'    => $data['valor'],
                'tipo'     => $data['tipo'],
            ]);

            return $movimentacao;
        });
    }
}
