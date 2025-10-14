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
        'inspection_state_class' => '\App\States\Inspection\InspectionState::class',
    ],
];
