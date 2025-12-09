<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default database table prefix used for package migrations
    |--------------------------------------------------------------------------
    */
    'table_prefix' => 'insp_',

    /*
    |--------------------------------------------------------------------------
    | Default class mapping
    |--------------------------------------------------------------------------
    */
    'classes' => [
        'inspection_state_class' => '\Dpb\Package\TaskMS\States\Inspection\InspectionState::class',
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation items order
    |--------------------------------------------------------------------------
    */
    'navigation' => [
        'inspection' => 1,
        'daily-maintenance' => 2,
        'upcomming-inspection' => 3,
        'inspection-template' => 4,
        'inspection-template-group' => 5,
    ],

];
