<?php

namespace App\Http\Controllers\FaturaPublica;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaturaPublicaResource;
use App\Models\FaturaShare;

class ShowFaturaPublica extends Controller
{
    /**
     * Acesso público (sem login) a uma fatura via token de compartilhamento.
     */
    public function __invoke(string $token): FaturaPublicaResource
    {
        $share = FaturaShare::where('token', $token)
            ->with(['pessoa', 'gasto.itens.pessoa'])
            ->firstOrFail();

        return new FaturaPublicaResource($share);
    }
}
