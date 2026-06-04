<?php

namespace App\Http\Controllers\CaixasFinaceiras;

use App\Http\Controllers\Controller;
use App\Http\Requests\CaixasFinaceiras\CreateCaixaFinanceiraRequest;
use App\Actions\CaixasFinaceiras\CreateCaixaFinanceiraAction;
use App\Http\Resources\CaixaFinanceiraResource;
use Illuminate\Http\Request;

class CreateCaixaFinanceira extends Controller
{
    public function __invoke(CreateCaixaFinanceiraRequest $request, CreateCaixaFinanceiraAction $action)
    {
        $caixaFinanceira = $action->execute($request->validated());

        return (new CaixaFinanceiraResource($caixaFinanceira))
            ->response()
            ->setStatusCode(201);
    }
}
