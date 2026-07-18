<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Visão pública de uma fatura acessada por um token de compartilhamento.
 * Expõe apenas o necessário para a pessoa marcar os itens dela — sem dados
 * do dono. O recurso encapsula um FaturaShare com `gasto.itens.pessoa` e
 * `pessoa` já carregados.
 */
class FaturaPublicaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Quem é a pessoa dona deste link (para marcar "é meu").
            'eu' => [
                'id'   => $this->pessoa->id,
                'nome' => $this->pessoa->nome,
                'cor'  => $this->pessoa->cor,
            ],
            'fatura' => [
                'id'          => $this->gasto->id,
                'descricao'   => $this->gasto->descricao,
                'data_gasto'  => $this->gasto->data_gasto,
                'valor_total' => $this->gasto->valor_total,
                'itens'       => GastoItemResource::collection($this->gasto->itens),
            ],
        ];
    }
}
