<?php

namespace App\Actions\CategoriasGastos;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeleteCategoriaGastoAction
{
    public function execute(int $id): void
    {
        DB::transaction(function () use ($id) {
            Auth::user()->categoriasGastos()->findOrFail($id)->delete();
        });
    }
}
