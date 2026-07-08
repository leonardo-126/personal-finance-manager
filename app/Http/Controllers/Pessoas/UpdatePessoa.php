<?php

namespace App\Http\Controllers\Pessoas;

use App\Actions\Pessoas\UpdatePessoaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pessoas\UpdatePessoaRequest;
use App\Http\Resources\PessoaResource;

class UpdatePessoa extends Controller
{
    public function __invoke(UpdatePessoaRequest $request, UpdatePessoaAction $action)
    {
        $pessoa = $action->execute((int) $request->route('id'), $request->validated());

        return (new PessoaResource($pessoa))
            ->response()
            ->setStatusCode(200);
    }
}
