<?php

namespace App\Http\Controllers\MovimentacoesCaixas;

use App\Http\Controllers\Controller;
use App\Http\Resources\MovimentacaoCaixaResource;
use Illuminate\Http\Request;

class ShowMovimentacaoCaixa extends Controller
{
    public function __invoke(Request $request)
    {
        return MovimentacaoCaixaResource::collection($request->user()->movimentacoesCaixas);
    }
}
