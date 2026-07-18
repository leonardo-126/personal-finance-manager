<?php

namespace App\Actions\Faturas\Compartilhamento;

use App\Models\FaturaShare;
use App\Models\GastosItens;
use Illuminate\Support\Facades\DB;

class MarcarItemPublicoAction
{
    /**
     * A partir de um token de compartilhamento, marca (ou desmarca) um item da
     * fatura como pertencente à pessoa dona daquele token.
     *
     * - `meu = true`  → atribui o item à pessoa do token.
     * - `meu = false` → só remove a atribuição se o item era desta pessoa
     *   (não mexe em itens atribuídos a outra pessoa).
     */
    public function execute(string $token, int $itemId, bool $meu): GastosItens
    {
        return DB::transaction(function () use ($token, $itemId, $meu) {
            $share = FaturaShare::where('token', $token)->firstOrFail();

            // O item precisa pertencer à fatura deste compartilhamento.
            $item = GastosItens::where('gasto_id', $share->gasto_id)
                ->findOrFail($itemId);

            if ($meu) {
                $item->pessoa_id = $share->pessoa_id;
            } elseif ($item->pessoa_id === $share->pessoa_id) {
                $item->pessoa_id = null;
            }

            $item->save();

            return $item->load('pessoa');
        });
    }
}
