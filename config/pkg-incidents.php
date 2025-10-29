<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default database table prefix used for package migrations
    |--------------------------------------------------------------------------
    */
    'table_prefix' => 'inc_',

    /*
    |--------------------------------------------------------------------------
    | Default class mapping
    |--------------------------------------------------------------------------
    */
    'classes' => [
        'incident_state_class' => '\App\States\Incident\IncidentState::class',
    ],
];
