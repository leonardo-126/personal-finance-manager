<?php

namespace App\Actions\CaixasFinaceiras;

use App\Models\CaixasFinanceiras;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateCaixaFinanceiraAction
{
    public function execute(int $id, array $data): CaixasFinanceiras
    {
        return DB::transaction(function () use ($id, $data) {
            $caixaFinanceira = Auth::user()->caixasFinanceiras()->findOrFail($id);

            $caixaFinanceira->update([
                'nome'      => $data['nome'],
                'descricao' => $data['descricao'] ?? null,
            ]);

            return $caixaFinanceira;
        });
    }
}
