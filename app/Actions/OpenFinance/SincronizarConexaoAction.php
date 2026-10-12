<?php

namespace App\Actions\OpenFinance;

use App\Models\OpenFinanceConexao;
use App\Models\OpenFinanceConta;
use App\Models\OpenFinanceTransacao;
use App\Services\OpenFinance\OpenFinanceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SincronizarConexaoAction
{
    private const LOTE = 500;

    public function __construct(private readonly OpenFinanceProvider $provider)
    {
    }

    /**
     * Atualiza status, contas e transações de uma conexão a partir do agregador.
     * Idempotente: rodar duas vezes não duplica nada, e transações já
     * importadas/ignoradas mantêm a decisão do usuário.
     */
    public function execute(OpenFinanceConexao $conexao): OpenFinanceConexao
    {
        $dados = $this->provider->buscarConexao($conexao->external_id);

        $conexao->update([
            'status'                  => $dados->status,
            'erro'                    => $dados->erro,
            'instituicao_id'          => $dados->instituicaoId ?? $conexao->instituicao_id,
            'instituicao_nome'        => $dados->instituicaoNome ?? $conexao->instituicao_nome,
            'instituicao_logo'        => $dados->instituicaoLogo ?? $conexao->instituicao_logo,
            'consentimento_expira_em' => $dados->consentimentoExpiraEm,
        ]);

        // Enquanto o agregador está coletando (ou pedindo ação do usuário) os dados estão incompletos.
        if (! $dados->status->permiteSincronizar()) {
            return $conexao;
        }

        $ate = Carbon::today();
        $de = $conexao->ultimo_sync_em
            ? $conexao->ultimo_sync_em->copy()->startOfDay()->subDays(config('open_finance.sincronizacao.dias_sobreposicao'))
            : $ate->copy()->subDays(config('open_finance.sincronizacao.dias_iniciais'));

        foreach ($this->provider->listarContas($conexao->external_id) as $contaData) {
            $conta = OpenFinanceConta::updateOrCreate(
                ['conexao_id' => $conexao->id, 'external_id' => $contaData->externalId],
                [
                    'user_id' => $conexao->user_id,
                    'tipo'    => $contaData->tipo,
                    'nome'    => $contaData->nome,
                    'numero'  => $contaData->numero,
                    'saldo'   => $contaData->saldo,
                    'moeda'   => $contaData->moeda,
                ],
            );

            $this->sincronizarTransacoes($conta, $de, $ate);
        }

        $conexao->update(['ultimo_sync_em' => now()]);

        return $conexao;
    }

    private function sincronizarTransacoes(OpenFinanceConta $conta, Carbon $de, Carbon $ate): void
    {
        $lote = [];

        foreach ($this->provider->listarTransacoes($conta->external_id, $de, $ate) as $transacao) {
            $lote[] = [
                'conta_id'          => $conta->id,
                'user_id'           => $conta->user_id,
                'external_id'       => $transacao->externalId,
                'data'              => $transacao->data->toDateString(),
                'descricao'         => mb_substr($transacao->descricao, 0, 255),
                'valor'             => round($transacao->valor, 2),
                'tipo'              => $transacao->tipo,
                'categoria_externa' => $transacao->categoria,
                'confirmada'        => $transacao->confirmada,
            ];

            if (count($lote) >= self::LOTE) {
                $this->gravar($lote);
                $lote = [];
            }
        }

        if ($lote) {
            $this->gravar($lote);
        }
    }

    private function gravar(array $lote): void
    {
        // `status` e `gasto_item_id` ficam de fora do update: são decisões do usuário.
        DB::transaction(fn () => OpenFinanceTransacao::upsert(
            $lote,
            ['conta_id', 'external_id'],
            ['data', 'descricao', 'valor', 'tipo', 'categoria_externa', 'confirmada', 'updated_at'],
        ));
    }
}
