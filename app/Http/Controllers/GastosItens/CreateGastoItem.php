<?php

namespace App\Http\Controllers\GastosItens;

use App\Http\Controllers\Controller;
use App\Http\Requests\GastosItens\CreateGastoItemRequest;
use App\Actions\GastosItens\CreateGastoItemAction;
use App\Http\Resources\GastoItemResource;

class CreateGastoItem extends Controller
{
    public function __invoke(CreateGastoItemRequest $request, CreateGastoItemAction $action)
    {
        $item = $action->execute($request->validated());

        return (new GastoItemResource($item))
            ->response()
            ->setStatusCode(201);
    }
}
