<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpenFinanceTransacaoResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'conta_id'          => $this->conta_id,
            'data'              => $this->data,
            'descricao'         => $this->descricao,
            'valor'             => $this->valor,
            'tipo'              => $this->tipo,
            'categoria_externa' => $this->categoria_externa,
            'confirmada'        => $this->confirmada,
            'status'            => $this->status,
            'gasto_item_id'     => $this->gasto_item_id,
        ];
    }
}
