<?php

namespace App\Http\Controllers\fontes_renda;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fontes_renda\UpdateFontesRenda as RendaUpdateFontesRenda;
use App\Http\Resources\FontesRendaResource;
use App\Actions\FontesRenda\UpdateFontesRendaAction;

class UpdateFontesRenda extends Controller
{
    public function __invoke(RendaUpdateFontesRenda $request, UpdateFontesRendaAction $action)
    {
        $fontes_renda = $action->execute((int) $request->route('id'), $request->validated());

        return (new FontesRendaResource($fontes_renda))
            ->response()
            ->setStatusCode(200);
    }
}
