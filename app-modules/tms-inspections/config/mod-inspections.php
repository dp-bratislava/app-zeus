<?php

use Dpb\Modules\Inspections\Filament\Resources\InspectionAssignmentResource;

return [

    /*
    |--------------------------------------------------------------------------
    | Filament pages
    |--------------------------------------------------------------------------
    */
    'filament_pages' => [
        // 'work_order_page' => [
        //     'navigation_label' => 'Pracovné príkazy',
        //     'slug' => 'work-orders',
        //     'view' => 'dpb-wtf-tms-bridge::filament.pages.work-order-page',
        //     'navigation_icon' => 'heroicon-o-clipboard-document-list',
        // ],
        // 'daily_maintenance_work_orders' => [
        //     'navigation_label' => 'Denné údržbové príkazy',
        //     'slug' => 'daily-maintenance-work-orders',
        //     'view' => 'dpb-wtf-tms-bridge::filament.pages.daily-maintenance-work-orders-page',
        //     'navigation_icon' => 'heroicon-o-calendar-days',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament resources
    |--------------------------------------------------------------------------
    */
    'filament_resources' => [
        InspectionAssignmentResource::class,
    ],
];

