<?php

namespace App\Providers;

use App\Services\OpenFinance\OpenFinanceProvider;
use App\Services\OpenFinance\Providers\FakeProvider;
use App\Services\OpenFinance\Providers\PluggyProvider;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OpenFinanceProvider::class, fn () => match (config('open_finance.provider')) {
            'pluggy' => new PluggyProvider(config('open_finance.providers.pluggy')),
            'fake'   => new FakeProvider(),
            default  => throw new InvalidArgumentException('OPEN_FINANCE_PROVIDER inválido: '.config('open_finance.provider')),
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
