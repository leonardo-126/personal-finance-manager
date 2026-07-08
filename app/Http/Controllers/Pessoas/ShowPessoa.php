<?php

namespace App\Http\Controllers\Pessoas;

use App\Http\Controllers\Controller;
use App\Http\Resources\PessoaResource;
use Illuminate\Http\Request;

class ShowPessoa extends Controller
{
    public function __invoke(Request $request)
    {
        return PessoaResource::collection($request->user()->pessoas);
    }
}
