<?php

namespace App\Actions\Rendas;

use App\Models\Rendas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateRendaAction
{
    public function execute(int $id, array $data): Rendas
    {
        return DB::transaction(function () use ($id, $data) {
            $renda = Auth::user()->rendas()->findOrFail($id);

            $renda->update([
                'fonte_renda_id'   => $data['fonte_renda_id'],
                'valor'            => $data['valor'],
                'data_recebimento' => $data['data_recebimento'],
                'descricao'        => $data['descricao'] ?? null,
            ]);

            return $renda;
        });
    }
}
