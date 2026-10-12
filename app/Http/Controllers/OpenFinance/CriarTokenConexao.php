<?php

namespace App\Http\Controllers\OpenFinance;

use App\Http\Controllers\Controller;
use App\Services\OpenFinance\OpenFinanceProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CriarTokenConexao extends Controller
{
    /**
     * Token para o front abrir o widget do agregador. Com `conexao_id`,
     * abre o widget para reautorizar uma conexão existente (consentimento expirado, erro de login).
     */
    public function __invoke(Request $request, OpenFinanceProvider $provider): JsonResponse
    {
        $request->validate(['conexao_id' => ['nullable', 'integer']]);

        $externalId = $request->filled('conexao_id')
            ? $request->user()->openFinanceConexoes()->findOrFail($request->integer('conexao_id'))->external_id
            : null;

        return response()->json([
            'data' => [
                'provider' => $provider->nome(),
                'token'    => $provider->criarTokenConexao($request->user()->id, $externalId),
            ],
        ]);
    }
}
