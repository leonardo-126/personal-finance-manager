<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RendaResource extends JsonResource
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
            'fonte_renda_id' => $this->fonte_renda_id,
            'valor' => $this->valor,
            'data_recebimento' => $this->data_recebimento,
            'descricao' => $this->descricao,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
