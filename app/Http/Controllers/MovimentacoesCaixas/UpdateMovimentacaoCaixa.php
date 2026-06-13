<?php

namespace App\Http\Controllers\MovimentacoesCaixas;

use App\Http\Controllers\Controller;
use App\Http\Requests\MovimentacoesCaixas\UpdateMovimentacaoCaixaRequest;
use App\Http\Resources\MovimentacaoCaixaResource;
use App\Actions\MovimentacoesCaixas\UpdateMovimentacaoCaixaAction;

class UpdateMovimentacaoCaixa extends Controller
{
    public function __invoke(UpdateMovimentacaoCaixaRequest $request, UpdateMovimentacaoCaixaAction $action)
    {
        $movimentacao = $action->execute((int) $request->route('id'), $request->validated());

        return (new MovimentacaoCaixaResource($movimentacao))
            ->response()
            ->setStatusCode(200);
    }
}
