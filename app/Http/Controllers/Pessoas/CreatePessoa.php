<?php

namespace App\Http\Controllers\Pessoas;

use App\Actions\Pessoas\CreatePessoaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pessoas\CreatePessoaRequest;
use App\Http\Resources\PessoaResource;

class CreatePessoa extends Controller
{
    public function __invoke(CreatePessoaRequest $request, CreatePessoaAction $action)
    {
        $pessoa = $action->execute($request->validated());

        return (new PessoaResource($pessoa))
            ->response()
            ->setStatusCode(201);
    }
}
