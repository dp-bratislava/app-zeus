<?php

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
            'label' => 'Prístup k oddeleniam',
            'is_multiple' => true,
            'type' => 'model',
            'type_detail' => Dpb\DatahubSync\Models\Department::class,
            'option_label' => 'title',
            'is_readonly' => false,
        ],
        [
            'key' => 'datahub-employee-id',
            'label' => 'Zamestnanec',
            'is_multiple' => false,
            'type' => 'model',
            'type_detail' => Dpb\DatahubSync\Models\Employee::class,
            'option_label' => 'fullName',
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