<?php

namespace App\Actions\GastosItens;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeleteGastoItemAction
{
    public function execute(int $id): void
    {
        DB::transaction(function () use ($id) {
            Auth::user()->gastosItens()->findOrFail($id)->delete();
        });
    }
}
