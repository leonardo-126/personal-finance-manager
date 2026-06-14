<?php

namespace App\Http\Controllers\fontes_renda;

use App\Http\Controllers\Controller;
use App\Actions\FontesRenda\DeleteFontesRendaAction;
use Illuminate\Http\Request;

class DeleteFontesRenda extends Controller
{
    public function __invoke(Request $request, DeleteFontesRendaAction $action)
    {
        $action->execute((int) $request->route('id'));

        return response()->noContent();
    }
}
