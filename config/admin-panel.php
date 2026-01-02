<?php

use Dpb\DpbEmployeeManager\Filament\Pages\EmployeeManagerPage\EmployeeManagerPage;
use Dpb\Insights\Filament\Pages\InsightsPage;
use Dpb\Package\TaskMS\UI\Filament\Plugins\TaskMSPlugin;
use Dpb\Package\TaskMS\UI\Filament\Resources\EAV\AttributeGroupResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\EAV\AttributeResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\BrandResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\MaintenanceGroupResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\VehicleGroupResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\VehicleModelResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\VehicleResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Fleet\VehicleTypeResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\DailyMaintenanceResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\InspectionAssignmentResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\InspectionTemplateGroupResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\InspectionTemplateResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Task\PlaceOfOriginResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Task\TaskGroupResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Task\TaskItemGroupResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Task\TaskItemResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Ticket\TicketAssignmentResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Ticket\TicketTypeResource;
use Dpb\UserAdmin\UserAdminPlugin;
use Dpb\WorkTimeFund\Filament\Pages\WorktimeSchedulePlannerPage;
use Dpb\WorkTimeFundFilament\Filament\Pages\WorktimeManagementPage;
use Dpb\WtfTmsBridge\Filament\Resources\Task\TaskAssignmentResource;

return [
    'plugins' => [
        UserAdminPlugin::class,
        TaskMSPlugin::class,
    ],

    'navigation' => [
        'items' => [
            TaskAssignmentResource::class,
            TaskItemResource::class,
            InspectionAssignmentResource::class,
            DailyMaintenanceResource::class,
            WorktimeManagementPage::class,
            WorktimeSchedulePlannerPage::class,
        ],
        'groups' => [
            [
                'title' => 'Ostatné',
                'items' => [
                    TicketAssignmentResource::class,
                    InsightsPage::class,
                    EmployeeManagerPage::class,
                ],
            ],
            [
                'title' => 'Číselníky',
                'items' => [
                    VehicleResource::class,
                    VehicleModelResource::class,
                    MaintenanceGroupResource::class,
                    VehicleGroupResource::class,
                    BrandResource::class,
                    VehicleTypeResource::class,
                    InspectionTemplateResource::class,
                    InspectionTemplateGroupResource::class,
                    TicketTypeResource::class,
                    PlaceOfOriginResource::class,
                    TaskGroupResource::class,
                    AttributeResource::class,
                    AttributeGroupResource::class,
                    TaskItemGroupResource::class,
                ],
            ],
        ],
    ],
];