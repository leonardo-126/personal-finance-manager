<?php

namespace App\Http\Controllers\Faturas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Faturas\PreviewFaturaRequest;
use App\Services\NubankFaturaParser;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class PreviewFatura extends Controller
{
    /**
     * Lê o arquivo da fatura e devolve as transações encontradas, sem persistir.
     * Permite ao usuário conferir o conteúdo antes de importar.
     */
    public function __invoke(PreviewFaturaRequest $request, NubankFaturaParser $parser): JsonResponse
    {
        try {
            $transacoes = $parser->parse($request->file('arquivo'));
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'data' => [
                'transacoes' => $transacoes,
                'total'      => round(array_sum(array_column($transacoes, 'valor')), 2),
                'quantidade' => count($transacoes),
            ],
        ]);
    }
}
