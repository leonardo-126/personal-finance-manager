<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GastoResource extends JsonResource
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
            'user_id' => $this->user_id,
            'caixa_id' => $this->caixa_id,
            'categoria_id' => $this->categoria_id,
            'valor_total' => $this->valor_total,
            'descricao' => $this->descricao,
            'is_fatura' => (bool) $this->is_fatura,
            'itens_count' => $this->whenCounted('itens'),
            'data_gasto' => $this->data_gasto,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
