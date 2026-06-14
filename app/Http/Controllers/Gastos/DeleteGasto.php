<?php

namespace App\Http\Controllers\Gastos;

use App\Http\Controllers\Controller;
use App\Actions\Gastos\DeleteGastoAction;
use Illuminate\Http\Request;

class DeleteGasto extends Controller
{
    public function __invoke(Request $request, DeleteGastoAction $action)
    {
        $action->execute((int) $request->route('id'));

        return response()->noContent();
    }
}
