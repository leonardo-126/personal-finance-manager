<?php

namespace App\Actions\Rendas;

use App\Models\Rendas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateRendaAction
{
    public function execute(array $data): Rendas
    {
        return DB::transaction(function () use ($data) {
            return Auth::user()->rendas()->create([
                'fonte_renda_id'   => $data['fonte_renda_id'],
                'valor'            => $data['valor'],
                'data_recebimento' => $data['data_recebimento'],
                'descricao'        => $data['descricao'] ?? null,
            ]);
        });
    }
}
