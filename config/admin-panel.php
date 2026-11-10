<?php

use App\Filament\Resources\ReportsResource;
use Dpb\DpbEmployeeManager\Filament\Pages\EmployeeManagerPage\EmployeeManagerPage;
use Dpb\Insights\Filament\Pages\InsightsPage;
use Dpb\Modules\Inspections\Filament\Plugins\InspectionPlugin;
use Dpb\Modules\Inspections\Filament\Resources\InspectionAssignmentResource;
use Dpb\Modules\Tasks\Filament\Plugins\TaskBatchPlugin;
use Dpb\Modules\Tasks\Filament\Resources\TaskBatches\TaskBatchResource;
use Dpb\Package\TaskMS\UI\Filament\Plugins\TaskMSPlugin;
use Dpb\Package\TaskMS\UI\Filament\Resources\EAV\AttributeGroupResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\EAV\AttributeResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Fleet;
// use Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\DailyMaintenanceResource;
// use Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\InspectionAssignmentResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\InspectionTemplateGroupResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\InspectionTemplateResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Task\PlaceOfOriginResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Task\TaskGroupResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Task\TaskItemGroupResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Task\TaskItemResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Ticket\TicketAssignmentResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Ticket\TicketTypeResource;
use Dpb\UserAdmin\Filament\Resources\RoleResource;
use Dpb\UserAdmin\Filament\Resources\UserResource;
use Dpb\UserAdmin\UserAdminPlugin;
use Dpb\WorkTimeFundFilament\Filament\Pages\WorktimeManagementPage;
use Dpb\WtfTmsBridge\Filament\Resources\TaskAssignmentResource;
use Dpb\WtfTmsBridge\Filament\Resources\Task\DailyMaintenanceResource;
use WatheqAlshowaiter\FilamentStickyTableHeader\StickyTableHeaderPlugin;

return [
    'plugins' => [
        UserAdminPlugin::class,
        TaskMSPlugin::class,
        StickyTableHeaderPlugin::class,
        InspectionPlugin::class,
        TaskBatchPlugin::class,
    ],

    'navigation' => [
        'items' => [
            TaskAssignmentResource::class,
            TaskItemResource::class,
            InspectionAssignmentResource::class,
            DailyMaintenanceResource::class,
            TaskBatchResource::class,
            WorktimeManagementPage::class,
        ],
        'groups' => [
            [
                'title' => 'Ostatné',
                'items' => [
                    TicketAssignmentResource::class,
                    InsightsPage::class,
                    EmployeeManagerPage::class,
                    ReportsResource::class,
                ],
            ],
            [
                'title' => 'Číselníky',
                'items' => [
                    // Fleet\VehicleResource::class,
                    // Fleet\VehicleModelResource::class,
                    // Fleet\MaintenanceGroupResource::class,
                    // Fleet\VehicleGroupResource::class,
                    // Fleet\BrandResource::class,
                    // Fleet\VehicleTypeResource::class,
                    // InspectionTemplateResource::class,
                    // InspectionTemplateGroupResource::class,
                    // TicketTypeResource::class,
                    // PlaceOfOriginResource::class,
                    // TaskGroupResource::class,
                    // AttributeResource::class,
                    // AttributeGroupResource::class,
                    // TaskItemGroupResource::class,
                ],
            ],
            [
                'title' => 'User Admin',
                'items' => [
                    UserResource::class,
                    RoleResource::class,
                ],
            ],
        ],
    ],
];
