<?php

namespace App\Actions\MovimentacoesCaixas;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeleteMovimentacaoCaixaAction
{
    public function execute(int $id): void
    {
        DB::transaction(function () use ($id) {
            Auth::user()->movimentacoesCaixas()->findOrFail($id)->delete();
        });
    }
}
