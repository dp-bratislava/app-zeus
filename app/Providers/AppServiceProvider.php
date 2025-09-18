<?php

namespace App\Providers;

use Dpb\Extension\ModelState\Providers\EventServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {    
        // app()->register(EventServiceProvider::class);    
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
            'activity-template' => \Dpb\Package\Activities\Models\ActivityTemplate::class,
            'inspection' => \App\Models\Inspection\Inspection::class,
            'inspection-template' => \App\Models\Inspection\InspectionTemplate::class,
            'ticket' => \Dpb\Package\Tickets\Models\Ticket::class,
            'user' => \App\Models\User::class,
            'vehicle-model' => \Dpb\Package\Fleet\Models\VehicleModel::class,
            'vehicle' => \Dpb\Package\Fleet\Models\Vehicle::class,
        ]);
    }
}
