<?php

namespace App\Providers;

use App\Filament\Resources\Fleet\Vehicle\VehicleResource\Components\VehicleCard;
use App\Filament\Resources\Fleet\Vehicle\VehicleResource\Components\VehicleModelList;
use App\Filament\Resources\TS\TicketItemResource\Components\TicketItemActivities;
use App\Filament\Resources\TS\TicketItemResource\Components\TicketItemMaterials;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\CachedObjectStorageFactory;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;

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

        FilamentAsset::register([
            Css::make('custom-styles', asset('css/app/custom-overrides.css')),
        ]);

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
            'ticket-item-group' => \Dpb\Package\Tickets\Models\TicketItemGroup::class,
            'ticket-source' => \Dpb\Package\Tickets\Models\TicketSource::class,
            'user' => \App\Models\User::class,
            'vehicle-model' => \Dpb\Package\Fleet\Models\VehicleModel::class,
            'vehicle' => \Dpb\Package\Fleet\Models\Vehicle::class,
            'maintenance-group' => \Dpb\Package\Fleet\Models\MaintenanceGroup::class,
            //

        ]);

        // Blade::component('ticket-item-activities', TicketItemActivities::class);
        Livewire::component('ticket-item-activities', TicketItemActivities::class);
        Livewire::component('ticket-item-materials', TicketItemMaterials::class);
        // fleet
        Livewire::component('fleet-vehicle-model-list', VehicleModelList::class);
        Livewire::component('fleet-vehicle-card', VehicleCard::class);

        // Cross-package relations for the "zariadenia" (asset movements) feature.
        // The vendor TaskItem / TaskItemGroup classes can't be edited, so the relations
        // that tie them to pkg-assets are attached at runtime.
        \Dpb\Package\Tasks\Models\TaskItem::resolveRelationUsing(
            'assetMovements',
            fn ($taskItem) => $taskItem->hasMany(
                \Dpb\Package\Assets\Models\AssetMovement::class,
                'task_item_id'
            )
        );
        \Dpb\Package\Tasks\Models\TaskItemGroup::resolveRelationUsing(
            'assetType',
            fn ($group) => $group->belongsTo(
                \Dpb\Package\Assets\Models\AssetType::class,
                'asset_type_id'
            )
        );
        // Slots (mounting positions) defined for a vehicle model — drives the per-model
        // positions manager. Cross-package: pkg-fleet's VehicleModel can't be edited.
        \Dpb\Package\Fleet\Models\VehicleModel::resolveRelationUsing(
            'slots',
            fn ($model) => $model->hasMany(
                \Dpb\Package\Assets\Models\AssetSlot::class,
                'fleet_vehicle_model_id'
            )
        );
    }
}
