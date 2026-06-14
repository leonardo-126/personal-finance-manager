<?php

namespace App\Http\Controllers\MovimentacoesCaixas;

use App\Http\Controllers\Controller;
use App\Actions\MovimentacoesCaixas\DeleteMovimentacaoCaixaAction;
use Illuminate\Http\Request;

class DeleteMovimentacaoCaixa extends Controller
{
    public function __invoke(Request $request, DeleteMovimentacaoCaixaAction $action)
    {
        $action->execute((int) $request->route('id'));

        return response()->noContent();
    }
}
