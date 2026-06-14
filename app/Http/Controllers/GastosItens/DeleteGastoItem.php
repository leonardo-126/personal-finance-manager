<?php

namespace App\Http\Controllers\GastosItens;

use App\Http\Controllers\Controller;
use App\Actions\GastosItens\DeleteGastoItemAction;
use Illuminate\Http\Request;

class DeleteGastoItem extends Controller
{
    public function __invoke(Request $request, DeleteGastoItemAction $action)
    {
        $action->execute((int) $request->route('id'));

        return response()->noContent();
    }
}
