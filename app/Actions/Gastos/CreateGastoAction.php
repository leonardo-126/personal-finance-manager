<?php

namespace App\Actions\Gastos;

use App\Models\Gastos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateGastoAction
{
    public function execute(array $data): Gastos
    {
        return DB::transaction(function () use ($data) {
            return Auth::user()->gastos()->create([
                'caixa_id'     => $data['caixa_id'],
                'categoria_id' => $data['categoria_id'],
                'valor_total'  => $data['valor_total'],
                'descricao'    => $data['descricao'] ?? null,
                'data_gasto'   => $data['data_gasto'],
            ]);
        });
    }
}
