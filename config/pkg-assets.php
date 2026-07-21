<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application-level State and Movement Enums
    |--------------------------------------------------------------------------
    |
    | These must implement the package interfaces so that the package can
    | operate without any hard dependency on concrete enum classes.
    |
    */
    'state_enum' => \App\Enums\AssetState::class,
    'movement_enum' => \App\Enums\MovementType::class,

];
