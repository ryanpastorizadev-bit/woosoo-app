<?php

namespace App\Providers;

use App\Contracts\PosContextGateway;
use App\Contracts\PosOrderGateway;
use App\Infrastructure\POS\FakePosContextGateway;
use App\Infrastructure\POS\FakePosOrderGateway;
use App\Infrastructure\POS\KryptonPosContextGateway;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PosOrderGateway::class, FakePosOrderGateway::class);
        $this->app->bind(PosContextGateway::class, function ($app): PosContextGateway {
            return config('pos.context_gateway') === 'krypton'
                ? $app->make(KryptonPosContextGateway::class)
                : $app->make(FakePosContextGateway::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
