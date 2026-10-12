<?php

namespace App\Http\Controllers\OpenFinance;

use App\Http\Controllers\Controller;
use App\Jobs\SincronizarConexaoOpenFinance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SincronizarConexao extends Controller
{
    /**
     * Agenda uma sincronização manual. O resultado aparece em GET /open-finance/conexoes.
     */
    public function __invoke(Request $request, int $id): JsonResponse
    {
        $conexao = $request->user()->openFinanceConexoes()->findOrFail($id);

        SincronizarConexaoOpenFinance::dispatch($conexao);

        return response()->json(['message' => 'Sincronização agendada.'], 202);
    }
}
