<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

use App\Models\ZoneForestiere;
use App\Models\Analyse;
use App\Models\Rapport;
use App\Policies\ZoneForestierePolicy;
use App\Policies\AnalysePolicy;
use App\Policies\RapportPolicy;

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
     * Enregistrement des Policies → Gate.
     */
    public function boot(): void
    {
        Gate::policy(ZoneForestiere::class, ZoneForestierePolicy::class);
        Gate::policy(Analyse::class,        AnalysePolicy::class);
        Gate::policy(Rapport::class,        RapportPolicy::class);
    }
}
