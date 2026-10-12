<?php

namespace App\Http\Controllers\OpenFinance;

use App\Http\Controllers\Controller;
use App\Jobs\SincronizarConexaoOpenFinance;
use App\Models\OpenFinanceConexao;
use App\Services\OpenFinance\OpenFinanceProvider;
use Illuminate\Http\Request;

class ReceberWebhook extends Controller
{
    /**
     * Endpoint público chamado pelo agregador. Autenticado por segredo
     * compartilhado; só agenda um sync, nunca confia nos dados do payload.
     */
    public function __invoke(Request $request, OpenFinanceProvider $provider)
    {
        $segredo = (string) config('open_finance.webhook.secret');
        $recebido = (string) $request->header(config('open_finance.webhook.header'));

        abort_if($segredo === '' || ! hash_equals($segredo, $recebido), 401);

        $externalId = $provider->conexaoDoWebhook($request->all());

        $conexao = $externalId
            ? OpenFinanceConexao::where('provider', $provider->nome())->where('external_id', $externalId)->first()
            : null;

        if ($conexao) {
            SincronizarConexaoOpenFinance::dispatch($conexao);
        }

        // Sempre 2xx: o agregador só precisa saber que recebemos.
        return response()->noContent();
    }
}
