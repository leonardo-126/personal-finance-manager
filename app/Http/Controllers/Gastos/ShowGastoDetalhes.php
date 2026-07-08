<?php

namespace App\Http\Controllers\Gastos;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaturaResource;
use Illuminate\Http\Request;

class ShowGastoDetalhes extends Controller
{
    /**
     * Retorna um gasto (ou fatura) do usuário com seus itens carregados,
     * para análise detalhada das transações.
     */
    public function __invoke(Request $request, int $id): FaturaResource
    {
        $gasto = $request->user()->gastos()->with('itens.pessoa')->findOrFail($id);

        return new FaturaResource($gasto);
    }
}
