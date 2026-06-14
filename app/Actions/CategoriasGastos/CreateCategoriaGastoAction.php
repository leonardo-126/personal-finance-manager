<?php

namespace App\Actions\CategoriasGastos;

use App\Models\CategoriasGastos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateCategoriaGastoAction
{
    public function execute(array $data): CategoriasGastos
    {
        return DB::transaction(function () use ($data) {
            return Auth::user()->categoriasGastos()->create([
                'nome' => $data['nome'],
            ]);
        });
    }
}
