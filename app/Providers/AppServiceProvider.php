<?php

namespace App\Providers;

use App\Filament\Resources\Fleet\Vehicle\VehicleResource\Components\VehicleCard;
use App\Filament\Resources\Fleet\Vehicle\VehicleResource\Components\VehicleModelList;
use App\Filament\Resources\TS\TicketItemResource\Components\TicketItemActivities;
use App\Filament\Resources\TS\TicketItemResource\Components\TicketItemMaterials;
use App\Models\DispatchReport;
use App\Models\User;
use App\Services\Asset\AppSpecificTransitionValidator;
use Dpb\Package\Activities\Models\Activity;
use Dpb\Package\Activities\Models\ActivityTemplate;
use Dpb\Package\Assets\Contracts\TransitionValidatorInterface;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Fleet\Models\VehicleModel;
use Dpb\Package\Incidents\Models\Incident;
use Dpb\Package\Inspections\Models\Inspection;
use Dpb\Package\Inspections\Models\InspectionTemplate;
use Dpb\Package\Tickets\Models\Ticket;
use Dpb\Package\Tickets\Models\TicketItem;
use Dpb\Package\Tickets\Models\TicketItemGroup;
use Dpb\Package\Tickets\Models\TicketSource;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
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
        $this->app->bind(
            TransitionValidatorInterface::class,
            AppSpecificTransitionValidator::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentAsset::register([
            Css::make('custom-styles', asset('css/app/custom-overrides.css')),
        ]);

        Relation::morphMap([
            'activity' => Activity::class,
            'activity-template' => ActivityTemplate::class,
            'dispatch-report' => DispatchReport::class,
            'inspection' => Inspection::class,
            'incident' => Incident::class,
            'inspection-template' => InspectionTemplate::class,
            'ticket' => Ticket::class,
            'ticket-item' => TicketItem::class,
            'ticket-item-group' => TicketItemGroup::class,
            'ticket-source' => TicketSource::class,
            'user' => User::class,
            'vehicle-model' => VehicleModel::class,
            'vehicle' => Vehicle::class,
            'maintenance-group' => MaintenanceGroup::class,
        ]);

        Livewire::component('ticket-item-activities', TicketItemActivities::class);
        Livewire::component('ticket-item-materials', TicketItemMaterials::class);
        Livewire::component('fleet-vehicle-model-list', VehicleModelList::class);
        Livewire::component('fleet-vehicle-card', VehicleCard::class);

    }
}
