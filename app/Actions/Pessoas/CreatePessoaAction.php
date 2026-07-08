<?php

namespace App\Actions\Pessoas;

use App\Models\Pessoas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreatePessoaAction
{
    public function execute(array $data): Pessoas
    {
        return DB::transaction(function () use ($data) {
            return Auth::user()->pessoas()->create([
                'nome'  => $data['nome'],
                'cor'   => $data['cor'] ?? null,
                'email' => $data['email'] ?? null,
            ]);
        });
    }
}
