<?php

namespace App\Jobs;

use App\Actions\OpenFinance\SincronizarConexaoAction;
use App\Models\OpenFinanceConexao;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Sincroniza uma conexão em background. Único por conexão para que uma
 * rajada de webhooks não dispare vários syncs simultâneos da mesma conta.
 */
class SincronizarConexaoOpenFinance implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [60, 300];

    public int $uniqueFor = 600;

    public function __construct(public OpenFinanceConexao $conexao)
    {
    }

    public function uniqueId(): string
    {
        return (string) $this->conexao->id;
    }

    public function handle(SincronizarConexaoAction $action): void
    {
        $action->execute($this->conexao);
    }
}
