<?php

namespace App\Http\Controllers\OpenFinance;

use App\Actions\OpenFinance\RegistrarConexaoAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\OpenFinance\RegistrarConexaoRequest;
use App\Http\Resources\OpenFinanceConexaoResource;

class RegistrarConexao extends Controller
{
    public function __invoke(RegistrarConexaoRequest $request, RegistrarConexaoAction $action)
    {
        $conexao = $action->execute($request->user(), $request->validated('external_id'));

        return (new OpenFinanceConexaoResource($conexao))
            ->response()
            ->setStatusCode(201);
    }
}
