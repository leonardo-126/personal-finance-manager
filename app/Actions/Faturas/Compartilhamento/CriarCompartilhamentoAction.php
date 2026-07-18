<?php

namespace App\Actions\Faturas\Compartilhamento;

use App\Models\FaturaShare;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CriarCompartilhamentoAction
{
    /**
     * Cria (ou retorna, se já existir) o link de compartilhamento de uma
     * fatura do usuário com uma de suas pessoas.
     */
    public function execute(User $user, int $gastoId, int $pessoaId): FaturaShare
    {
        // Garante que a fatura e a pessoa pertencem ao usuário autenticado.
        $gasto = $user->gastos()->findOrFail($gastoId);
        $pessoa = $user->pessoas()->findOrFail($pessoaId);

        $share = FaturaShare::firstOrCreate(
            ['gasto_id' => $gasto->id, 'pessoa_id' => $pessoa->id],
            ['token' => FaturaShare::gerarToken()],
        );

        return $share->load('pessoa');
    }
}
