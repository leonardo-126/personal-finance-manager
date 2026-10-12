<?php

namespace App\Http\Controllers\OpenFinance;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpenFinance\AtualizarContaRequest;
use App\Http\Resources\OpenFinanceContaResource;

class AtualizarConta extends Controller
{
    /**
     * Vincula (ou desvincula) a conta a um caixa, usado como padrão na importação.
     */
    public function __invoke(AtualizarContaRequest $request, int $id)
    {
        $conta = $request->user()->openFinanceContas()->findOrFail($id);
        $conta->update(['caixa_id' => $request->validated('caixa_id')]);

        return new OpenFinanceContaResource($conta);
    }
}
