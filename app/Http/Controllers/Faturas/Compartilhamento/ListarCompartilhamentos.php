<?php

namespace App\Http\Controllers\Faturas\Compartilhamento;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaturaShareResource;
use App\Models\FaturaShare;
use Illuminate\Http\Request;

class ListarCompartilhamentos extends Controller
{
    /**
     * Lista os links de compartilhamento existentes de uma fatura do usuário.
     */
    public function __invoke(Request $request, int $id)
    {
        $gasto = $request->user()->gastos()->findOrFail($id);

        $shares = FaturaShare::where('gasto_id', $gasto->id)
            ->with('pessoa')
            ->get();

        return FaturaShareResource::collection($shares);
    }
}
