<?php

namespace App\Actions\FontesRenda;

use App\Models\FontesRenda;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateFontesRendaAction
{
    public function execute(int $id, array $data): FontesRenda
    {
        return DB::transaction(function () use ($id, $data) {
            $fontesRenda = Auth::user()->fontesRenda()->findOrFail($id);

            $fontesRenda->update([
                'nome'      => $data['nome'],
                'tipo'      => $data['tipo'],
                'descricao' => $data['descricao'] ?? null,
                'status'    => $data['status'],
            ]);

            return $fontesRenda;
        });
    }
}
