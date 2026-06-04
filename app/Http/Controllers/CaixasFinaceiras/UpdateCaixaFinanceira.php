<?php

namespace App\Http\Controllers\CaixasFinaceiras;

use App\Http\Controllers\Controller;
use App\Http\Requests\CaixasFinaceiras\UpdateCaixaFinanceiraRequest;
use App\Http\Resources\CaixaFinanceiraResource;
use App\Actions\CaixasFinaceiras\UpdateCaixaFinanceiraAction;

class UpdateCaixaFinanceira extends Controller
{
    public function __invoke(UpdateCaixaFinanceiraRequest $request, UpdateCaixaFinanceiraAction $action)
    {
        $caixaFinanceira = $action->execute((int) $request->route('id'), $request->validated());

        return (new CaixaFinanceiraResource($caixaFinanceira))
            ->response()
            ->setStatusCode(200);
    }
}
