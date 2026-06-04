<?php

namespace App\Http\Controllers\fontes_renda;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fontes_renda\CreateFontesRenda as Fontes_rendaCreateFontesRenda;
use App\Http\Resources\FontesRendaResource;
use App\Actions\FontesRenda\CreateFontesRendaAction;

class createFontesRenda extends Controller
{
    public function __invoke(Fontes_rendaCreateFontesRenda $request, CreateFontesRendaAction $action)
    {
        $fontes_renda = $action->execute($request->validated());

        return (new FontesRendaResource($fontes_renda))
            ->response()
            ->setStatusCode(201);
    }
}
