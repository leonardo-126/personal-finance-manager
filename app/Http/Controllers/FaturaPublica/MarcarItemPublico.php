<?php

namespace App\Http\Controllers\FaturaPublica;

use App\Actions\Faturas\Compartilhamento\MarcarItemPublicoAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Faturas\MarcarItemPublicoRequest;
use App\Http\Resources\GastoItemResource;

class MarcarItemPublico extends Controller
{
    /**
     * Marca/desmarca um item da fatura como pertencente à pessoa do token.
     */
    public function __invoke(MarcarItemPublicoRequest $request, MarcarItemPublicoAction $action, string $token, int $itemId)
    {
        $item = $action->execute(
            $token,
            $itemId,
            (bool) $request->validated('meu'),
        );

        return (new GastoItemResource($item))
            ->response()
            ->setStatusCode(200);
    }
}
