<?php

namespace App\Actions\Faturas\Compartilhamento;

use App\Models\FaturaShare;
use App\Models\User;

class RevogarCompartilhamentoAction
{
    /**
     * Revoga (remove) o link de compartilhamento de uma fatura com uma pessoa.
     * O link deixa de funcionar imediatamente.
     */
    public function execute(User $user, int $gastoId, int $pessoaId): void
    {
        // Garante que a fatura pertence ao usuário autenticado.
        $gasto = $user->gastos()->findOrFail($gastoId);

        FaturaShare::where('gasto_id', $gasto->id)
            ->where('pessoa_id', $pessoaId)
            ->delete();
    }
}
