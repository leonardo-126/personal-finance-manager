<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Link de compartilhamento visto pelo dono da fatura (contém o token para
 * montar a URL). O front compõe a URL final com o próprio origin.
 */
class FaturaShareResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'gasto_id'  => $this->gasto_id,
            'pessoa_id' => $this->pessoa_id,
            'token'     => $this->token,
            'pessoa'    => $this->whenLoaded('pessoa', fn () => $this->pessoa ? [
                'id'   => $this->pessoa->id,
                'nome' => $this->pessoa->nome,
                'cor'  => $this->pessoa->cor,
            ] : null),
            'created_at' => $this->created_at,
        ];
    }
}
