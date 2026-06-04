<?php

use App\Models\Rendas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateRendaAction
{
    public function execute(array $data): Rendas
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();
            
            return $user->rendas()->create([
                'valor' => $data['valor'],
                'data_recebimento' => $data['data_recebimento'],
                'descricao' => $data['descricao'] ?? null,
                'fontes_renda_id' => $data['fontes_renda_id'],
            ]);
        });
    }
}