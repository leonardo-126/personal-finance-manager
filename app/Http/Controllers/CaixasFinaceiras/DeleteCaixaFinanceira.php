<?php

namespace App\Http\Controllers\CaixasFinaceiras;

use App\Http\Controllers\Controller;
use App\Actions\CaixasFinaceiras\DeleteCaixaFinanceiraAction;
use Illuminate\Http\Request;

class DeleteCaixaFinanceira extends Controller
{
    public function __invoke(Request $request, DeleteCaixaFinanceiraAction $action)
    {
        $action->execute((int) $request->route('id'));

        return response()->noContent();
    }
}
