<?php

use Illuminate\Foundation\Inspiring;
use App\Enums\StatusConexaoOpenFinance;
use App\Jobs\SincronizarConexaoOpenFinance;
use App\Models\OpenFinanceConexao;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Rede de segurança para webhooks perdidos (e o único gatilho em dev, sem URL pública).
Artisan::command('open-finance:sincronizar', function () {
    $total = 0;
    OpenFinanceConexao::where('status', '!=', StatusConexaoOpenFinance::ErroLogin)
        ->each(function (OpenFinanceConexao $conexao) use (&$total) {
            SincronizarConexaoOpenFinance::dispatch($conexao);
            $total++;
        });

    $this->info("{$total} sincronizações agendadas.");
})->purpose('Agenda a sincronização de todas as conexões de Open Finance');

Schedule::command('open-finance:sincronizar')->dailyAt('06:00');
