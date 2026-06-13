<?php

namespace App\Http\Controllers\MovimentacoesCaixas;

use App\Http\Controllers\Controller;
use App\Http\Requests\MovimentacoesCaixas\CreateMovimentacaoCaixaRequest;
use App\Actions\MovimentacoesCaixas\CreateMovimentacaoCaixaAction;
use App\Http\Resources\MovimentacaoCaixaResource;

class CreateMovimentacaoCaixa extends Controller
{
    public function __invoke(CreateMovimentacaoCaixaRequest $request, CreateMovimentacaoCaixaAction $action)
    {
        $movimentacao = $action->execute($request->validated());

        return (new MovimentacaoCaixaResource($movimentacao))
            ->response()
            ->setStatusCode(201);
    }
}
