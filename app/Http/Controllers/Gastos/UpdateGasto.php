<?php

namespace App\Http\Controllers\Gastos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gastos\UpdateGastoRequest;
use App\Http\Resources\GastoResource;
use App\Actions\Gastos\UpdateGastoAction;

class UpdateGasto extends Controller
{
    public function __invoke(UpdateGastoRequest $request, UpdateGastoAction $action)
    {
        $gasto = $action->execute((int) $request->route('id'), $request->validated());

        return (new GastoResource($gasto))
            ->response()
            ->setStatusCode(200);
    }
}
