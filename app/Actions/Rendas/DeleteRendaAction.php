<?php

namespace App\Actions\Rendas;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeleteRendaAction
{
    public function execute(int $id): void
    {
        DB::transaction(function () use ($id) {
            Auth::user()->rendas()->findOrFail($id)->delete();
        });
    }
}
