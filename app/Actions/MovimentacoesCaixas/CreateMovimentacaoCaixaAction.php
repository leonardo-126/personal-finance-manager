<?php

namespace App\Actions\MovimentacoesCaixas;

use App\Models\MovimentacoesCaixas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateMovimentacaoCaixaAction
{
    public function execute(array $data): MovimentacoesCaixas
    {
        return DB::transaction(function () use ($data) {
            return Auth::user()->movimentacoesCaixas()->create([
                'caixa_id' => $data['caixa_id'],
                'renda_id' => $data['renda_id'] ?? null,
                'valor'    => $data['valor'],
                'tipo'     => $data['tipo'],
            ]);
        });
    }
}
