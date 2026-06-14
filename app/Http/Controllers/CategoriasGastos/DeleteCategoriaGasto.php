<?php

namespace App\Http\Controllers\CategoriasGastos;

use App\Http\Controllers\Controller;
use App\Actions\CategoriasGastos\DeleteCategoriaGastoAction;
use Illuminate\Http\Request;

class DeleteCategoriaGasto extends Controller
{
    public function __invoke(Request $request, DeleteCategoriaGastoAction $action)
    {
        $action->execute((int) $request->route('id'));

        return response()->noContent();
    }
}
