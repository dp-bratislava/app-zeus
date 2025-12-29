<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Filament pages
    |--------------------------------------------------------------------------
    */
    'filament_pages' => [
        'work_order_page' => [
            'navigation_label' => 'Pracovné príkazy',
            'slug' => 'work-orders',
            'view' => 'dpb-wtf-tms-bridge::filament.pages.work-order-page',
            'navigation_icon' => 'heroicon-o-clipboard-document-list',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament resources
    |--------------------------------------------------------------------------
    */
    'filament_resources' => [
        // tasks
        Dpb\WtfTmsBridge\Filament\Resources\Task\TaskAssignmentResource::class,
    ],
];
