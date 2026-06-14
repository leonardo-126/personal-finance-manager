<?php

namespace App\Actions\GastosItens;

use App\Models\GastosItens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateGastoItemAction
{
    public function execute(int $id, array $data): GastosItens
    {
        return DB::transaction(function () use ($id, $data) {
            $gasto = Auth::user()->gastos()->findOrFail($data['gasto_id']);

            $item = $gasto->itens()->findOrFail($id);

            $item->update([
                'nome'   => $data['nome'],
                'valor'  => $data['valor'],
                'motivo' => $data['motivo'] ?? null,
            ]);

            return $item;
        });
    }
}
