<?php

namespace App\Http\Controllers\Faturas;

use App\Actions\Faturas\ImportarFaturaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Faturas\ImportarFaturaRequest;
use App\Http\Resources\FaturaResource;
use App\Services\NubankFaturaParser;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class ImportarFatura extends Controller
{
    /**
     * Importa a fatura: cria um gasto com um item por transação do arquivo.
     */
    public function __invoke(
        ImportarFaturaRequest $request,
        NubankFaturaParser $parser,
        ImportarFaturaAction $action,
    ): JsonResponse {
        try {
            $transacoes = $parser->parse($request->file('arquivo'));
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $gasto = $action->execute($transacoes, $request->validated());

        return (new FaturaResource($gasto))
            ->response()
            ->setStatusCode(201);
    }
}
