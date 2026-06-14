<?php

namespace App\Http\Controllers\GastosItens;

use App\Http\Controllers\Controller;
use App\Http\Resources\GastoItemResource;
use Illuminate\Http\Request;

class ShowGastoItem extends Controller
{
    public function __invoke(Request $request)
    {
        return GastoItemResource::collection($request->user()->gastosItens);
    }
}
