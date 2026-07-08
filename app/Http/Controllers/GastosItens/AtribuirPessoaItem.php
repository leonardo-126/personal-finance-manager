<?php

namespace App\Http\Controllers\GastosItens;

use App\Actions\GastosItens\AtribuirPessoaItemAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\GastosItens\AtribuirPessoaItemRequest;
use App\Http\Resources\GastoItemResource;

class AtribuirPessoaItem extends Controller
{
    public function __invoke(AtribuirPessoaItemRequest $request, AtribuirPessoaItemAction $action)
    {
        $item = $action->execute(
            (int) $request->route('id'),
            $request->validated('pessoa_id'),
        );

        return (new GastoItemResource($item))
            ->response()
            ->setStatusCode(200);
    }
}
