<?php

namespace App\Services\OpenFinance\Data;

use Carbon\CarbonInterface;

final readonly class TransacaoData
{
    /**
     * @param  float  $valor  sempre positivo
     * @param  'debito'|'credito'  $tipo  debito = saiu dinheiro / compra no cartão
     */
    public function __construct(
        public string $externalId,
        public CarbonInterface $data,
        public string $descricao,
        public float $valor,
        public string $tipo,
        public ?string $categoria = null,
        public bool $confirmada = true,
    ) {
    }
}
