<?php

namespace App\Http\Controllers\Rendas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rendas\UpdateRenda as UpdateRendaRequest;
use App\Http\Resources\RendaResource;
use App\Actions\Rendas\UpdateRendaAction;

class UpdateRenda extends Controller
{
    public function __invoke(UpdateRendaRequest $request, UpdateRendaAction $action)
    {
        $renda = $action->execute((int) $request->route('id'), $request->validated());

        return (new RendaResource($renda))
            ->response()
            ->setStatusCode(200);
    }
}
