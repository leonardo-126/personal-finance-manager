<?php

namespace App\Http\Controllers\OpenFinance;

use App\Enums\StatusTransacaoOpenFinance;
use App\Http\Controllers\Controller;
use App\Http\Resources\OpenFinanceConexaoResource;
use Illuminate\Http\Request;

class ListarConexoes extends Controller
{
    /**
     * Conexões do usuário com as contas e a quantidade de transações aguardando conciliação.
     */
    public function __invoke(Request $request)
    {
        $conexoes = $request->user()->openFinanceConexoes()
            ->with(['contas' => fn ($q) => $q->withCount([
                'transacoes' => fn ($t) => $t->where('status', StatusTransacaoOpenFinance::Pendente),
            ])])
            ->latest()
            ->get();

        return OpenFinanceConexaoResource::collection($conexoes);
    }
}
