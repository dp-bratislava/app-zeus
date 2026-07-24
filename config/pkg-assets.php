<?php

use Dpb\WtfTmsBridge\Enums\AssetState;
use Dpb\WtfTmsBridge\Enums\MovementType;

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
    'state_enum' => AssetState::class,
    'movement_enum' => MovementType::class,

];
