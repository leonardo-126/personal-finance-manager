<?php

namespace App\Http\Controllers\Pessoas;

use App\Actions\Pessoas\DeletePessoaAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeletePessoa extends Controller
{
    public function __invoke(Request $request, DeletePessoaAction $action)
    {
        $action->execute((int) $request->route('id'));

        return response()->noContent();
    }
}
