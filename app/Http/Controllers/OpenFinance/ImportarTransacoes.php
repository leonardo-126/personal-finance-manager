<?php

namespace App\Http\Controllers\OpenFinance;

use App\Actions\OpenFinance\ImportarTransacoesCartaoAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\OpenFinance\ImportarTransacoesRequest;
use App\Http\Resources\FaturaResource;

class ImportarTransacoes extends Controller
{
    /**
     * Cria uma fatura a partir das transações selecionadas de um cartão.
     */
    public function __invoke(ImportarTransacoesRequest $request, ImportarTransacoesCartaoAction $action, int $id)
    {
        $gasto = $action->execute($request->user(), $id, $request->validated());

        return (new FaturaResource($gasto))
            ->response()
            ->setStatusCode(201);
    }
}
