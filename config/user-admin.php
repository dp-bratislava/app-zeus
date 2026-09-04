<?php

use Dpb\DatahubSync\Models\Department;
use Dpb\DatahubSync\Models\Employee;
use Dpb\Package\Fleet\Models\MaintenanceGroup;
use Dpb\Package\Fleet\Models\VehicleType;

return [
    /*
    |--------------------------------------------------------------------------
    | Default User Password
    |--------------------------------------------------------------------------
    |
    | This value will be used as the initial password for new users or for
    | accounts that have been reset. It is strongly recommended to prompt
    | users to change this password after first login.
    |
    */
    'default-password' => '0000',
    /*
    |--------------------------------------------------------------------------
    | Default User Color
    |--------------------------------------------------------------------------
    |
    | The default color assigned to a user, defined as a HEX value.
    | This can be used for visual identification in UI elements,
    | charts, or labels throughout the application.
    |
    */
    'default-color' => '#000000',
    /*
    |--------------------------------------------------------------------------
    | User Parameters
    |--------------------------------------------------------------------------
    |
    | These are customizable parameters assigned to individual users.
    | Each parameter can represent various types of data, such as a
    | model reference or a simple value. The configuration defines
    | whether the parameter allows multiple values, whether it's
    | read-only, how it should be displayed, and what type of
    | data it refers to. These settings help control and
    | personalize the behavior of the application per user.
    |
    */
    'parameters' => [
        [
            'key' => 'available-departments',
            'label' => 'Prístup ku strediskám',
            'is_multiple' => true,
            'type' => 'model',
            'type_detail' => Department::class,
            'option_label' => 'titleWithCode',
            'is_readonly' => false,
        ],
        [
            'key' => 'datahub-employee-id',
            'label' => 'Zamestnanec',
            'is_multiple' => false,
            'type' => 'model',
            'type_detail' => Employee::class,
            'eager_load' => ['primaryContract'],
            'scope' => 'active',
            'option_label' => 'fullNameWithPid',
            'is_readonly' => false,
        ],
        [
            'key' => 'fleet-vehicle-type-id',
            'label' => 'Typ vozidla',
            'is_multiple' => true,
            'type' => 'model',
            'type_detail' => VehicleType::class,
            // 'scope' => 'active',
            'option_label' => 'title',
            'is_readonly' => false,
        ],
        [
            'key' => 'fleet-maintenance-group-id',
            'label' => 'Technická prevádzka',
            'is_multiple' => true,
            'type' => 'model',
            'type_detail' => MaintenanceGroup::class,
            // 'scope' => 'active',
            'option_label' => 'title',
            'is_readonly' => false,
        ],
        /*
        [
            'key' => 'value',
            'label' => 'Hodnota',
            'is_multiple' => false,
            'type' => 'string',
            'type_detail' => '',
            'option_label' => '',
            'is_readonly' => false,
        ],
        */
    ],
];
