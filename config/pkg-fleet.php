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
        'vehicle_state_class' => '\App\States\Fleet\Vehicle\VehicleState::class',
    ],    

    /*
    |--------------------------------------------------------------------------
    | Navigation items order
    |--------------------------------------------------------------------------
    */
    'navigation' => [
        'vehicle' => 1,
        'vehicle-model' => 2,
        'maintenance-group' => 3,
        'vehicle-group' => 4,
        'brand' => 5,
        'vehicle-type' => 6,
    ],    
        
];
