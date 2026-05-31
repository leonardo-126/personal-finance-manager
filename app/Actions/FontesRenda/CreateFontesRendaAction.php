<?php

use App\Models\FontesRenda;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateFontesRendaAction
{
    public function execute(array $data): FontesRenda
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();
            
            return $user->fontesRenda()->create([
                'nome' => $data['nome'],
                'tipo' => $data['tipo'],
                'descricao' => $data['descricao'] ?? null,
            ]);
        });
    }
}
