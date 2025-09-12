<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

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
        // All future morphs *must* be mapped!
        // Relation::enforceMorphMap([
        //     'ticket' => \App\Models\TS\Ticket::class,
        //     'user' => \App\Models\User::class,
        //     'vehicle' => \App\Models\Fleet\Vehicle::class,
        // ]);
        Relation::morphMap([
            'inspection' => \App\Models\Inspection\Inspection::class,
            'inspection-template' => \App\Models\Inspection\InspectionTemplate::class,
            'ticket' => \App\Models\TS\Ticket::class,
            'user' => \App\Models\User::class,
            'vehicle' => \App\Models\Fleet\Vehicle::class,
            'vehicle-model' => \App\Models\Fleet\VehicleModel::class,
        ]);
    }
}
