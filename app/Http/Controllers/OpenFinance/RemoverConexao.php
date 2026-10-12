<?php

namespace App\Http\Controllers\OpenFinance;

use App\Actions\OpenFinance\RemoverConexaoAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RemoverConexao extends Controller
{
    public function __invoke(Request $request, RemoverConexaoAction $action, int $id)
    {
        $action->execute($request->user(), $id);

        return response()->noContent();
    }
}
