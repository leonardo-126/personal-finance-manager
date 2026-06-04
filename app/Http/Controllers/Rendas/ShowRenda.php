<?php

namespace App\Http\Controllers\Rendas;

use App\Http\Controllers\Controller;
use App\Http\Resources\RendaResource;
use Illuminate\Http\Request;

class ShowRenda extends Controller
{
    public function __invoke(Request $request)
    {
        return RendaResource::collection($request->user()->rendas);
    }
}
