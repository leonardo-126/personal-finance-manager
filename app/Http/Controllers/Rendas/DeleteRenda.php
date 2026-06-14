<?php

namespace App\Http\Controllers\Rendas;

use App\Http\Controllers\Controller;
use App\Actions\Rendas\DeleteRendaAction;
use Illuminate\Http\Request;

class DeleteRenda extends Controller
{
    public function __invoke(Request $request, DeleteRendaAction $action)
    {
        $action->execute((int) $request->route('id'));

        return response()->noContent();
    }
}
