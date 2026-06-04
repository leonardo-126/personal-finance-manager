<?php

use App\Models\Rendas;
use Illuminate\Support\Facades\DB;

class UpdateRendaAction
{
    public function execute(array $data, Rendas $renda): Rendas
    {
        return DB::transaction(function () use ($data, $renda) {
            $renda->update([
                'valor' => $data['valor'],
                'data_recebimento' => $data['data_recebimento'],
                'descricao' => $data['descricao'] ?? null,
                'fontes_renda_id' => $data['fontes_renda_id'],
                'status' => $data['status'],
            ]);

            return $renda;
        });
    }
}