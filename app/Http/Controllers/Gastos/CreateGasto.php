<?php

namespace App\Http\Controllers\Gastos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gastos\CreateGastoRequest;
use App\Actions\Gastos\CreateGastoAction;
use App\Http\Resources\GastoResource;

class CreateGasto extends Controller
{
    public function __invoke(CreateGastoRequest $request, CreateGastoAction $action)
    {
        $gasto = $action->execute($request->validated());

        return (new GastoResource($gasto))
            ->response()
            ->setStatusCode(201);
    }
}
