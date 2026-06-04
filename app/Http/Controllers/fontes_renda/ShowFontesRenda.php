<?php

namespace App\Http\Controllers\fontes_renda;

use App\Http\Controllers\Controller;
use App\Http\Resources\FontesRendaResource;
use Illuminate\Http\Request;

class ShowFontesRenda extends Controller
{
    public function __invoke(Request $request)
    {
        return FontesRendaResource::collection($request->user()->fontesRenda);
    }
}
