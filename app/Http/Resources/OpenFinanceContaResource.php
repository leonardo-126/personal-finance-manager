<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpenFinanceContaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'conexao_id'          => $this->conexao_id,
            'caixa_id'            => $this->caixa_id,
            'tipo'                => $this->tipo,
            'nome'                => $this->nome,
            // Nunca devolvemos o número completo.
            'numero'              => $this->numeroMascarado(),
            'saldo'               => $this->saldo,
            'moeda'               => $this->moeda,
            'transacoes_pendentes' => $this->whenCounted('transacoes'),
        ];
    }
}
