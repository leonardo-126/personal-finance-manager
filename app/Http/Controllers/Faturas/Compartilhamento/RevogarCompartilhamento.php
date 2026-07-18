<?php

namespace App\Http\Controllers\Faturas\Compartilhamento;

use App\Actions\Faturas\Compartilhamento\RevogarCompartilhamentoAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RevogarCompartilhamento extends Controller
{
    /**
     * Revoga o link de compartilhamento de uma fatura com uma pessoa.
     */
    public function __invoke(Request $request, RevogarCompartilhamentoAction $action, int $id, int $pessoaId)
    {
        $action->execute($request->user(), $id, $pessoaId);

        return response()->noContent();
    }
}
