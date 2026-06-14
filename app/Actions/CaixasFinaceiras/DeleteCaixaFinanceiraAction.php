<?php

namespace App\Actions\CaixasFinaceiras;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeleteCaixaFinanceiraAction
{
    public function execute(int $id): void
    {
        DB::transaction(function () use ($id) {
            Auth::user()->caixasFinanceiras()->findOrFail($id)->delete();
        });
    }
}
