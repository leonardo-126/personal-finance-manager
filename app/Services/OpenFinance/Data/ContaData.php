<?php

namespace App\Services\OpenFinance\Data;

use App\Enums\TipoContaOpenFinance;

final readonly class ContaData
{
    public function __construct(
        public string $externalId,
        public TipoContaOpenFinance $tipo,
        public string $nome,
        public ?string $numero = null,
        public ?float $saldo = null,
        public string $moeda = 'BRL',
    ) {
    }
}
