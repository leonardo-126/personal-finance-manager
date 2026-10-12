<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpenFinanceConexaoResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'provider'                => $this->provider,
            'instituicao_nome'        => $this->instituicao_nome,
            'instituicao_logo'        => $this->instituicao_logo,
            'status'                  => $this->status,
            'erro'                    => $this->erro,
            'consentimento_expira_em' => $this->consentimento_expira_em,
            'ultimo_sync_em'          => $this->ultimo_sync_em,
            'contas'                  => OpenFinanceContaResource::collection($this->whenLoaded('contas')),
            'created_at'              => $this->created_at,
        ];
    }
}
