<?php

namespace App\Http\Controllers\Rendas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rendas\CreateRenda as CreateRendaRequest;
use App\Http\Resources\RendaResource;
use Illuminate\Http\Request;
use UpdateRendaAction;

class UpdateRenda extends Controller
{
    public function __invoke(CreateRendaRequest $request, UpdateRendaAction $action)
    {
        $renda = $action->execute($request->validated(), $request->route('id'));

        return (new RendaResource($renda))
            ->response()
            ->setStatusCode(200);
    }
}
