<?php

namespace App\Actions\CaixasFinaceiras;

use App\Models\CaixasFinanceiras;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateCaixaFinanceiraAction
{
    public function execute(array $data): CaixasFinanceiras
    {
        return DB::transaction(function () use ($data) {
            return Auth::user()->caixasFinanceiras()->create([
                'nome'      => $data['nome'],
                'descricao' => $data['descricao'] ?? null,
            ]);
        });
    }
}
