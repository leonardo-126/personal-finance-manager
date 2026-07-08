<?php

namespace App\Actions\Pessoas;

use App\Models\Pessoas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdatePessoaAction
{
    public function execute(int $id, array $data): Pessoas
    {
        return DB::transaction(function () use ($id, $data) {
            $pessoa = Auth::user()->pessoas()->findOrFail($id);

            $pessoa->update([
                'nome'  => $data['nome'],
                'cor'   => $data['cor'] ?? null,
                'email' => $data['email'] ?? null,
            ]);

            return $pessoa;
        });
    }
}
