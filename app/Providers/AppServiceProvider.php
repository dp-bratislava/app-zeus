<?php

namespace App\Providers;

use App\Filament\Resources\TS\TicketItemResource\Components\TicketItemActivities;
use App\Filament\Resources\TS\TicketItemResource\Components\TicketItemMaterials;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
            'activity' => \Dpb\Package\Activities\Models\Activity::class,
            'activity-template' => \Dpb\Package\Activities\Models\ActivityTemplate::class,
            'dispatch-report' => \App\Models\DispatchReport::class,
            'inspection' => \Dpb\Package\Inspections\Models\Inspection::class,
            'incident' => \Dpb\Package\Incidents\Models\Incident::class,
            'inspection-template' => \Dpb\Package\Inspections\Models\InspectionTemplate::class,
            'ticket' => \Dpb\Package\Tickets\Models\Ticket::class,
            'ticket-item' => \Dpb\Package\Tickets\Models\TicketItem::class,
            'user' => \App\Models\User::class,
            'vehicle-model' => \Dpb\Package\Fleet\Models\VehicleModel::class,
            'vehicle' => \Dpb\Package\Fleet\Models\Vehicle::class,
            'maintenance-group' => \Dpb\Package\Fleet\Models\MaintenanceGroup::class,
        ]);

        // Blade::component('ticket-item-activities', TicketItemActivities::class);
        Livewire::component('ticket-item-activities', TicketItemActivities::class);
        Livewire::component('ticket-item-materials', TicketItemMaterials::class);
    }
}
