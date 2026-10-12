<?php

namespace App\Services\OpenFinance\Providers;

use App\Enums\StatusConexaoOpenFinance;
use App\Enums\TipoContaOpenFinance;
use App\Services\OpenFinance\Data\ConexaoData;
use App\Services\OpenFinance\Data\ContaData;
use App\Services\OpenFinance\Data\TransacaoData;
use App\Services\OpenFinance\OpenFinanceProvider;
use Carbon\CarbonInterface;

/**
 * Agregador fictício para desenvolvimento local e testes: toda conexão vem
 * ativa, com uma conta corrente e um cartão, e transações determinísticas
 * (os mesmos ids a cada chamada, o que permite testar a idempotência do sync).
 */
class FakeProvider implements OpenFinanceProvider
{
    public function nome(): string
    {
        return 'fake';
    }

    public function criarTokenConexao(int $userId, ?string $externalId = null): string
    {
        return "fake-connect-token-{$userId}";
    }

    public function buscarConexao(string $externalId): ConexaoData
    {
        return new ConexaoData(
            externalId: $externalId,
            status: StatusConexaoOpenFinance::Ativa,
            instituicaoId: 'fake-bank',
            instituicaoNome: 'Banco Fictício',
        );
    }

    public function listarContas(string $externalId): array
    {
        return [
            new ContaData("{$externalId}-corrente", TipoContaOpenFinance::Corrente, 'Conta Corrente', '0001234567', 2500.00),
            new ContaData("{$externalId}-cartao", TipoContaOpenFinance::Cartao, 'Cartão de Crédito', '5555444433331111', 730.40),
        ];
    }

    public function listarTransacoes(string $contaExternalId, CarbonInterface $de, CarbonInterface $ate): iterable
    {
        $modelos = str_ends_with($contaExternalId, '-cartao')
            ? [
                ['Mercado Central', 312.90, 'debito', 'Supermercado'],
                ['Posto Ipiranga', 180.00, 'debito', 'Transporte'],
                ['Streaming', 39.90, 'debito', 'Lazer'],
                ['Estorno Streaming', 39.90, 'credito', 'Lazer'],
            ]
            : [
                ['Salário', 5000.00, 'credito', 'Renda'],
                ['Aluguel', 1800.00, 'debito', 'Moradia'],
                ['Pix recebido', 150.00, 'credito', 'Transferência'],
            ];

        foreach ($modelos as $i => [$descricao, $valor, $tipo, $categoria]) {
            $data = $ate->copy()->subDays($i * 3);
            if ($data->lt($de)) {
                continue;
            }

            yield new TransacaoData("{$contaExternalId}-tx-{$i}", $data, $descricao, $valor, $tipo, $categoria);
        }
    }

    public function removerConexao(string $externalId): void
    {
    }

    public function conexaoDoWebhook(array $payload): ?string
    {
        return $payload['itemId'] ?? null;
    }
}
