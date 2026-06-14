<?php

namespace App\Actions\Gastos;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeleteGastoAction
{
    public function execute(int $id): void
    {
        DB::transaction(function () use ($id) {
            Auth::user()->gastos()->findOrFail($id)->delete();
        });
    }
}
