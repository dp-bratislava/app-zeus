<?php

use Dpb\Package\TaskMS\UI\Filament\Resources\EAV\AttributeGroupResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\EAV\AttributeResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\BrandResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\MaintenanceGroupResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\VehicleGroupResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\VehicleModelResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\VehicleResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\VehicleTypeResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\HR\DepartmentAssignmentResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\InspectionAssignmentResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\InspectionTemplateGroupResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\InspectionTemplateResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\UpcomingInspectionResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Reports\VehicleStatusReportResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Task\PlaceOfOriginResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Task\TaskGroupResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Task\TaskItemGroupResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Task\TaskItemResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Ticket\TicketAssignmentResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Ticket\TicketTypeResource;

return [
    /*
    |--------------------------------------------------------------------------
    | Filament pages
    |--------------------------------------------------------------------------
    */
    'filament_pages' => [
        'tickets' => [
            // 'navigation_label' => 'Pracovné príkazy',
            // 'slug' => 'work-orders',
            // 'view' => 'dpb-wtf-tms-bridge::filament.pages.work-order-page',
            // 'navigation_icon' => 'heroicon-o-clipboard-document-list',
            'navigation_group' => 'Ostatné',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation items order
    |--------------------------------------------------------------------------
    */
    'filament_resources' => [
        // eav
        AttributeResource::class,
        AttributeGroupResource::class,
        // tasks
        TaskGroupResource::class,
        TaskItemGroupResource::class,
        TaskItemResource::class,
        // Dpb\Package\TaskMS\UI\Filament\Resources\Task\TaskAssignmentResource::class,
        PlaceOfOriginResource::class,
        // tickets
        TicketAssignmentResource::class,
        TicketTypeResource::class,
        // inspections
        InspectionAssignmentResource::class,
        // Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\DailyMaintenanceResource::class,
        InspectionTemplateGroupResource::class,
        InspectionTemplateResource::class,
        UpcomingInspectionResource::class,
        // // fleet
        // Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\DailyExpeditionResource::class,
        MaintenanceGroupResource::class,
        VehicleResource::class,
        BrandResource::class,
        VehicleGroupResource::class,
        VehicleModelResource::class,
        VehicleTypeResource::class,
        // reports
        VehicleStatusReportResource::class,
        // HR
        DepartmentAssignmentResource::class,

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
            'department-assignments',
        ],
    ],
];
