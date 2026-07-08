<?php

namespace App\Http\Controllers\Faturas;

use App\Http\Controllers\Controller;
use App\Http\Resources\GastoResource;
use Illuminate\Http\Request;

class ShowFaturas extends Controller
{
    /**
     * Lista apenas os gastos que são faturas de cartão, com a contagem de itens.
     */
    public function __invoke(Request $request)
    {
        $faturas = $request->user()->gastos()
            ->where('is_fatura', true)
            ->withCount('itens')
            ->orderByDesc('data_gasto')
            ->get();

        return GastoResource::collection($faturas);
    }
}
