<?php

namespace App\Http\Controllers\CategoriasGastos;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriaGastoResource;
use Illuminate\Http\Request;

class ShowCategoriaGasto extends Controller
{
    public function __invoke(Request $request)
    {
        return CategoriaGastoResource::collection($request->user()->categoriasGastos);
    }
}
