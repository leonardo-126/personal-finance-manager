<?php

namespace App\Actions\GastosItens;

use App\Models\GastosItens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateGastoItemAction
{
    public function execute(array $data): GastosItens
    {
        return DB::transaction(function () use ($data) {
            $gasto = Auth::user()->gastos()->findOrFail($data['gasto_id']);

            $item = $gasto->itens()->create([
                'nome'   => $data['nome'],
                'valor'  => $data['valor'],
                'motivo' => $data['motivo'] ?? null,
            ]);

            $gasto->recalcularTotal();

            return $item;
        });
    }
}
