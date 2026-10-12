<?php

namespace App\Actions\OpenFinance;

use App\Models\User;
use App\Services\OpenFinance\OpenFinanceException;
use App\Services\OpenFinance\OpenFinanceProvider;

class RemoverConexaoAction
{
    public function __construct(private readonly OpenFinanceProvider $provider)
    {
    }

    /**
     * Revoga o consentimento no agregador e apaga contas/transações em staging.
     * Gastos já importados continuam existindo.
     */
    public function execute(User $user, int $id): void
    {
        $conexao = $user->openFinanceConexoes()->findOrFail($id);

        try {
            $this->provider->removerConexao($conexao->external_id);
        } catch (OpenFinanceException $e) {
            // Não prende o usuário a uma conexão que ele quer remover; fica registrado para revogar manualmente.
            report($e);
        }

        $conexao->delete();
    }
}
