<?php

namespace App\Http\Controllers\CaixasFinaceiras;

use App\Http\Controllers\Controller;
use App\Http\Resources\CaixaFinanceiraResource;
use Illuminate\Http\Request;

class ShowCaixaFinanceira extends Controller
{
    public function __invoke(Request $request)
    {
        return CaixaFinanceiraResource::collection($request->user()->caixasFinanceiras);
    }
}
