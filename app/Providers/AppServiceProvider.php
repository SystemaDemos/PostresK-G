<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // 1. Asegúrate de importar esto
use Illuminate\Support\Facades\Schema; // 2. Importa esto también
use App\Models\TasaCambio; // 3. Importa tu modelo

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Verificamos si la tabla existe para evitar errores al correr migraciones desde cero
        if (Schema::hasTable('tasas_cambio')) {
            // Buscamos la última tasa guardada
            $tasaActiva = TasaCambio::latest()->first()?->valor ?? 0.00;

            // Compartimos la variable con TODO el sistema
            View::share('tasaActiva', $tasaActiva);
        } else {
            View::share('tasaActiva', 0.00);
        }
    }
}
