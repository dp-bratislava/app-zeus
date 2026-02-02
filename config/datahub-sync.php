<?php

use Dpb\DatahubSync\Models;

return [
    'server' => [
        'host' => env('DATAHUB_HOST', 'https://datahub.dpb.sk'),
        'token' => env('DATAHUB_ACCESS_TOKEN', ''),
    ],

    /**
     * Define the models that should be updated, along with the specific columns
     * that are subject to synchronization or tracking.
     * The array should use model class names as keys and an array of column names as values.
     * IMPORTANT: Dont change order of models
     */
    'models' => [

        Models\Location::class => [
            'enabled' => false,
            'columns' => '*',
        ],

        Models\Hierarchy::class => [
            'enabled' => true,
            'columns' => '*',
        ],

        Models\Material::class => [
            'enabled' => false,
            'columns' => [
                'code',
                'title',
                'measure_unit',
                'type',
            ],
        ],

        Models\ContractType::class => [
            'enabled' => true,
            'columns' => '*',
        ],

        Models\Department::class => [
            'enabled' => true,
            'columns' => '*',
        ],

        Models\EmployeeCircuit::class => [
            'enabled' => true,
            'columns' => '*',
        ],

        Models\Employee::class => [
            'enabled' => true,
            'columns' => '*',
        ],

        Models\Profession::class => [
            'enabled' => true,
            'columns' => '*',
        ],

        Models\EmployeeContract::class => [
            'enabled' => true,
            'columns' => '*',
        ],

        /**
         * Attendance
         */
        Models\Attendance\Group::class => [
            'enabled' => false,
            'columns' => '*',
        ],

        Models\Attendance\Shift::class => [
            'enabled' => true,
            'columns' => '*',
        ],

        Models\Attendance\AbsenceType::class => [
            'enabled' => false,
            'columns' => '*',
        ],

        Models\Attendance\Attendance::class => [
            'enabled' => false,
            'columns' => '*',
        ],
    ],
];
