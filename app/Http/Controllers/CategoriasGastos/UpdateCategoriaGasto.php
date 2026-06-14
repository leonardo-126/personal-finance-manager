<?php

namespace App\Http\Controllers\CategoriasGastos;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoriasGastos\UpdateCategoriaGastoRequest;
use App\Http\Resources\CategoriaGastoResource;
use App\Actions\CategoriasGastos\UpdateCategoriaGastoAction;

class UpdateCategoriaGasto extends Controller
{
    public function __invoke(UpdateCategoriaGastoRequest $request, UpdateCategoriaGastoAction $action)
    {
        $categoria = $action->execute((int) $request->route('id'), $request->validated());

        return (new CategoriaGastoResource($categoria))
            ->response()
            ->setStatusCode(200);
    }
}
