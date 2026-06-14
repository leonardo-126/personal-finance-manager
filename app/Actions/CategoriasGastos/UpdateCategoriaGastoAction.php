<?php

namespace App\Actions\CategoriasGastos;

use App\Models\CategoriasGastos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateCategoriaGastoAction
{
    public function execute(int $id, array $data): CategoriasGastos
    {
        return DB::transaction(function () use ($id, $data) {
            $categoria = Auth::user()->categoriasGastos()->findOrFail($id);

            $categoria->update([
                'nome' => $data['nome'],
            ]);

            return $categoria;
        });
    }
}
