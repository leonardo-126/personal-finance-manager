<?php

use App\Services\OpenFinance\OpenFinanceException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi(); 
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Falha no agregador de Open Finance: o problema é externo, não do request.
        $exceptions->render(fn (OpenFinanceException $e) => response()->json([
            'message' => 'O serviço de Open Finance está indisponível no momento. Tente novamente em instantes.',
        ], 502));
    })->create();
