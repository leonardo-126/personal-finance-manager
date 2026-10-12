<?php

namespace App\Services\OpenFinance;

use App\Services\OpenFinance\Data\ConexaoData;
use App\Services\OpenFinance\Data\ContaData;
use App\Services\OpenFinance\Data\TransacaoData;
use Carbon\CarbonInterface;

/**
 * Contrato de um agregador de Open Finance (Pluggy, Belvo, ...).
 *
 * Os drivers traduzem o formato do agregador para os DTOs de Data\ e lançam
 * OpenFinanceException em qualquer falha de comunicação. Nada fora de
 * App\Services\OpenFinance\Providers deve conhecer o formato do agregador.
 */
interface OpenFinanceProvider
{
    /** Nome curto gravado em open_finance_conexoes.provider. */
    public function nome(): string;

    /**
     * Token de curta duração que o front usa para abrir o widget de conexão
     * do agregador. Com $externalId, abre o widget para reautorizar uma conexão existente.
     */
    public function criarTokenConexao(int $userId, ?string $externalId = null): string;

    public function buscarConexao(string $externalId): ConexaoData;

    /** @return list<ContaData> */
    public function listarContas(string $externalId): array;

    /** @return iterable<TransacaoData> Percorre todas as páginas. */
    public function listarTransacoes(string $contaExternalId, CarbonInterface $de, CarbonInterface $ate): iterable;

    /** Revoga o consentimento no agregador. */
    public function removerConexao(string $externalId): void;

    /**
     * Lê o payload de um webhook e devolve o id externo da conexão que
     * precisa ser ressincronizada, ou null se o evento deve ser ignorado.
     */
    public function conexaoDoWebhook(array $payload): ?string;
}
