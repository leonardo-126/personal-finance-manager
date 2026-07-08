<?php

namespace App\Actions\GastosItens;

use App\Models\GastosItens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AtribuirPessoaItemAction
{
    /**
     * Atribui (ou remove, com pessoa_id nulo) a pessoa responsável por um item.
     */
    public function execute(int $id, ?int $pessoaId): GastosItens
    {
        return DB::transaction(function () use ($id, $pessoaId) {
            $item = Auth::user()->gastosItens()->findOrFail($id);

            // Garante que a pessoa pertence ao usuário autenticado.
            if ($pessoaId !== null) {
                Auth::user()->pessoas()->findOrFail($pessoaId);
            }

            $item->update(['pessoa_id' => $pessoaId]);

            return $item->load('pessoa');
        });
    }
}
