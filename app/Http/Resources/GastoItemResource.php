<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GastoItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'gasto_id' => $this->gasto_id,
            'nome' => $this->nome,
            'valor' => $this->valor,
            'motivo' => $this->motivo,
            'data_transacao' => $this->data_transacao,
            'pessoa_id' => $this->pessoa_id,
            'pessoa' => $this->whenLoaded('pessoa', fn () => $this->pessoa ? [
                'id'   => $this->pessoa->id,
                'nome' => $this->pessoa->nome,
                'cor'  => $this->pessoa->cor,
            ] : null),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
