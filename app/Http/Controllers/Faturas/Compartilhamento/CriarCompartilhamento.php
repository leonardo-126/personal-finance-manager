<?php

namespace App\Http\Controllers\Faturas\Compartilhamento;

use App\Actions\Faturas\Compartilhamento\CriarCompartilhamentoAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Faturas\CriarCompartilhamentoRequest;
use App\Http\Resources\FaturaShareResource;

class CriarCompartilhamento extends Controller
{
    /**
     * Cria (ou retorna) o link de compartilhamento de uma fatura com uma pessoa.
     */
    public function __invoke(CriarCompartilhamentoRequest $request, CriarCompartilhamentoAction $action, int $id)
    {
        $share = $action->execute(
            $request->user(),
            $id,
            (int) $request->validated('pessoa_id'),
        );

        return (new FaturaShareResource($share))
            ->response()
            ->setStatusCode(201);
    }
}
