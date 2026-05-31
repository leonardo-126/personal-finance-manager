<?php 

use App\Models\FontesRenda;
use Illuminate\Support\Facades\DB;

class UpdateFontesRendaAction
{
    public function execute(FontesRenda $fontesRenda, array $data): FontesRenda
    {
        return DB::transaction(function () use ($fontesRenda, $data) {
            $fontesRenda->update([
                'nome' => $data['nome'],
                'tipo' => $data['tipo'],
                'descricao' => $data['descricao'] ?? null,
                'status' => $data['status'],
            ]);

            return $fontesRenda;
        });
    }
}