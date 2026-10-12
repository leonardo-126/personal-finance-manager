<?php

namespace App\Actions\OpenFinance;

use App\Jobs\SincronizarConexaoOpenFinance;
use App\Models\OpenFinanceConexao;
use App\Models\User;
use App\Services\OpenFinance\OpenFinanceProvider;
use Illuminate\Auth\Access\AuthorizationException;

class RegistrarConexaoAction
{
    public function __construct(private readonly OpenFinanceProvider $provider)
    {
    }

    /**
     * Registra a conexão criada pelo widget do agregador e agenda o primeiro sync.
     * Chamado pelo front logo após o usuário concluir o consentimento.
     */
    public function execute(User $user, string $externalId): OpenFinanceConexao
    {
        $dados = $this->provider->buscarConexao($externalId);

        // O connect token leva o id do usuário; uma conexão de outra pessoa não pode ser "adotada".
        if ($dados->clientUserId !== null && $dados->clientUserId !== (string) $user->id) {
            throw new AuthorizationException('Esta conexão não pertence ao usuário autenticado.');
        }

        $conexao = OpenFinanceConexao::firstOrNew([
            'provider'    => $this->provider->nome(),
            'external_id' => $dados->externalId,
        ]);

        if ($conexao->exists && $conexao->user_id !== $user->id) {
            throw new AuthorizationException('Esta conexão não pertence ao usuário autenticado.');
        }

        $conexao->fill([
            'user_id'                 => $user->id,
            'status'                  => $dados->status,
            'erro'                    => $dados->erro,
            'instituicao_id'          => $dados->instituicaoId,
            'instituicao_nome'        => $dados->instituicaoNome,
            'instituicao_logo'        => $dados->instituicaoLogo,
            'consentimento_expira_em' => $dados->consentimentoExpiraEm,
        ])->save();

        SincronizarConexaoOpenFinance::dispatch($conexao);

        return $conexao;
    }
}
