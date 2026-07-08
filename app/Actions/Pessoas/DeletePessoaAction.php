<?php

namespace App\Actions\Pessoas;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeletePessoaAction
{
    public function execute(int $id): void
    {
        DB::transaction(function () use ($id) {
            Auth::user()->pessoas()->findOrFail($id)->delete();
        });
    }
}
