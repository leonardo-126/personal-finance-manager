<?php

namespace App\Actions\FontesRenda;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeleteFontesRendaAction
{
    public function execute(int $id): void
    {
        DB::transaction(function () use ($id) {
            Auth::user()->fontesRenda()->findOrFail($id)->delete();
        });
    }
}
