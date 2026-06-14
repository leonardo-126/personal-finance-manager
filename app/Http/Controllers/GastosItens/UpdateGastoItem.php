<?php

namespace App\Http\Controllers\GastosItens;

use App\Http\Controllers\Controller;
use App\Http\Requests\GastosItens\UpdateGastoItemRequest;
use App\Http\Resources\GastoItemResource;
use App\Actions\GastosItens\UpdateGastoItemAction;

class UpdateGastoItem extends Controller
{
    public function __invoke(UpdateGastoItemRequest $request, UpdateGastoItemAction $action)
    {
        $item = $action->execute((int) $request->route('id'), $request->validated());

        return (new GastoItemResource($item))
            ->response()
            ->setStatusCode(200);
    }
}
