<?php

namespace App\Http\Controllers\OpenFinance;

use App\Http\Controllers\Controller;
use App\Http\Resources\OpenFinanceTransacaoResource;
use Illuminate\Http\Request;

class ListarTransacoes extends Controller
{
    /**
     * Transações recebidas do banco, filtráveis por conta e status (padrão: pendentes).
     */
    public function __invoke(Request $request)
    {
        $filtros = $request->validate([
            'conta_id' => ['nullable', 'integer'],
            'status'   => ['nullable', 'in:pendente,importada,ignorada'],
        ]);

        $transacoes = $request->user()->openFinanceTransacoes()
            ->when($filtros['conta_id'] ?? null, fn ($q, $contaId) => $q->where('conta_id', $contaId))
            ->where('status', $filtros['status'] ?? 'pendente')
            ->orderByDesc('data')
            ->orderByDesc('id')
            ->paginate(100);

        return OpenFinanceTransacaoResource::collection($transacoes);
    }
}
