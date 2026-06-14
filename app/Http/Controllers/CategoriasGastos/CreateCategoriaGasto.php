<?php

namespace App\Http\Controllers\CategoriasGastos;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoriasGastos\CreateCategoriaGastoRequest;
use App\Actions\CategoriasGastos\CreateCategoriaGastoAction;
use App\Http\Resources\CategoriaGastoResource;

class CreateCategoriaGasto extends Controller
{
    public function __invoke(CreateCategoriaGastoRequest $request, CreateCategoriaGastoAction $action)
    {
        $categoria = $action->execute($request->validated());

        return (new CategoriaGastoResource($categoria))
            ->response()
            ->setStatusCode(201);
    }
}
