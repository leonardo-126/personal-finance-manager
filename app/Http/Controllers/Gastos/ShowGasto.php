<?php

namespace App\Http\Controllers\Gastos;

use App\Http\Controllers\Controller;
use App\Http\Resources\GastoResource;
use Illuminate\Http\Request;

class ShowGasto extends Controller
{
    public function __invoke(Request $request)
    {
        return GastoResource::collection($request->user()->gastos);
    }
}
