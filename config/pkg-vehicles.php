<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default database table prefix used for package migrations
    |--------------------------------------------------------------------------
    */
    'table_prefix' => 'fleet_',

    /*
    |--------------------------------------------------------------------------
    | Default class mapping
    |--------------------------------------------------------------------------
    */
    'classes' => [
        'vehicle_state_class' => '\Dpb\Package\TaskMS\States\Fleet\Vehicle\VehicleState::class',
    ],  
];
