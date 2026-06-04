<?php

namespace App\Http\Controllers\Rendas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rendas\CreateRenda as CreateRendaRequest;
use CreateRendaAction;
use App\Http\Resources\RendaResource;
use Illuminate\Http\Request;

class CreateRenda extends Controller
{
    public function __invoke(CreateRendaRequest $request, CreateRendaAction $action)
    {
        $renda = $action->execute($request->validated());

        return (new RendaResource($renda))
            ->response()
            ->setStatusCode(201);
    }
}
