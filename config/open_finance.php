<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Provedor de Open Finance
    |--------------------------------------------------------------------------
    |
    | Agregador usado para conectar as contas do usuário. O resto do sistema
    | só conhece o contrato App\Services\OpenFinance\OpenFinanceProvider, então
    | trocar de agregador é escrever um novo driver e mudar esta chave.
    |
    | Suportados: "pluggy", "fake" (dados fictícios para desenvolvimento/testes).
    |
    */

    'provider' => env('OPEN_FINANCE_PROVIDER', 'fake'),

    /*
    | Janela de busca de transações. Na primeira sincronização de uma conta
    | buscamos `dias_iniciais` para trás; nas seguintes, a partir do último
    | sync menos `dias_sobreposicao` (transações pendentes mudam por alguns dias).
    */
    'sincronizacao' => [
        'dias_iniciais'     => (int) env('OPEN_FINANCE_DIAS_INICIAIS', 90),
        'dias_sobreposicao' => (int) env('OPEN_FINANCE_DIAS_SOBREPOSICAO', 7),
    ],

    /*
    | Segredo compartilhado enviado pelo agregador no header abaixo em toda
    | chamada do webhook. Sem ele configurado o webhook recusa tudo.
    */
    'webhook' => [
        'header' => env('OPEN_FINANCE_WEBHOOK_HEADER', 'X-Webhook-Secret'),
        'secret' => env('OPEN_FINANCE_WEBHOOK_SECRET'),
        'url'    => env('OPEN_FINANCE_WEBHOOK_URL'),
    ],

    'providers' => [
        'pluggy' => [
            'base_url'      => env('PLUGGY_BASE_URL', 'https://api.pluggy.ai'),
            'client_id'     => env('PLUGGY_CLIENT_ID'),
            'client_secret' => env('PLUGGY_CLIENT_SECRET'),
            'timeout'       => (int) env('PLUGGY_TIMEOUT', 20),
        ],
    ],

];
