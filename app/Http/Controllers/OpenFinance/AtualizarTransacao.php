<?php

namespace App\Http\Controllers\OpenFinance;

use App\Enums\StatusTransacaoOpenFinance;
use App\Http\Controllers\Controller;
use App\Http\Requests\OpenFinance\AtualizarTransacaoRequest;
use App\Http\Resources\OpenFinanceTransacaoResource;

class AtualizarTransacao extends Controller
{
    /**
     * Ignora uma transação (ex.: pagamento da fatura) ou volta ela para pendente.
     */
    public function __invoke(AtualizarTransacaoRequest $request, int $id)
    {
        $transacao = $request->user()->openFinanceTransacoes()
            ->where('status', '!=', StatusTransacaoOpenFinance::Importada)
            ->findOrFail($id);

        $transacao->update(['status' => $request->validated('status')]);

        return new OpenFinanceTransacaoResource($transacao);
    }
}
