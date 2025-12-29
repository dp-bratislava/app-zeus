<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Navigation items order
    |--------------------------------------------------------------------------
    */
    'filament_resources' => [
        // eav
        Dpb\Package\TaskMS\UI\Filament\Resources\EAV\AttributeResource::class,
        Dpb\Package\TaskMS\UI\Filament\Resources\EAV\AttributeGroupResource::class,
        // tasks
        Dpb\Package\TaskMS\UI\Filament\Resources\Task\TaskGroupResource::class,
        Dpb\Package\TaskMS\UI\Filament\Resources\Task\TaskItemGroupResource::class,
        Dpb\Package\TaskMS\UI\Filament\Resources\Task\TaskItemResource::class,
        // Dpb\Package\TaskMS\UI\Filament\Resources\Task\TaskAssignmentResource::class,
        Dpb\Package\TaskMS\UI\Filament\Resources\Task\PlaceOfOriginResource::class,
        // tickets
        Dpb\Package\TaskMS\UI\Filament\Resources\Ticket\TicketAssignmentResource::class,
        Dpb\Package\TaskMS\UI\Filament\Resources\Ticket\TicketTypeResource::class,
        // inspections
        Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\InspectionAssignmentResource::class,
        Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\DailyMaintenanceResource::class,
        Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\InspectionTemplateGroupResource::class,
        Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\InspectionTemplateResource::class,
        Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\UpcomingInspectionResource::class,
        // // fleet
        // Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\DailyExpeditionResource::class,
        Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\MaintenanceGroupResource::class,
        Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\VehicleResource::class,
        Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\BrandResource::class,
        Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\VehicleGroupResource::class,
        Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\VehicleModelResource::class,
        Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\VehicleTypeResource::class,
        // reports
        Dpb\Package\TaskMS\UI\Filament\Resources\Reports\VehicleStatusReportResource::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation items order
    |--------------------------------------------------------------------------
    */
    'navigation' => [
        'enums' => [
            'vehicle',
            'vehicle-model',
            'maintenance-group',
            'vehicle-group',
            'vehicle-brand',
            'vehicle-type',
            // 'inspection' => 1,
            // 'daily-maintenance' => 2,
            // 'upcomming-inspection' => 3,
            'inspection-template',
            'inspection-template-group',
            // 'ticket' => 1,
            // 'ticket-item' => 2,
            'ticket-type',
            'place-of-origin',
            'task-group',
            'task-item-group',
        ],
    ],
];

