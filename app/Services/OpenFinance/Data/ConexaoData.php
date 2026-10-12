<?php

namespace App\Services\OpenFinance\Data;

use App\Enums\StatusConexaoOpenFinance;
use Carbon\CarbonInterface;

final readonly class ConexaoData
{
    public function __construct(
        public string $externalId,
        public StatusConexaoOpenFinance $status,
        public ?string $instituicaoId = null,
        public ?string $instituicaoNome = null,
        public ?string $instituicaoLogo = null,
        public ?string $erro = null,
        public ?CarbonInterface $consentimentoExpiraEm = null,
        // Id do nosso usuário que o agregador associou à conexão (enviado no connect token).
        public ?string $clientUserId = null,
    ) {
    }
}
