<?php

return [
    'server' => [
        'host' => env('DATAHUB_HOST', 'https://datahub.dpb.sk'),
        'token' => env('DATAHUB_ACCESS_TOKEN', ''),
    ],

    'columns' => [
        'employees' => [
            'first_name',
            'last_name',
            'prefix_titles',
            'suffix_titles',
            'hash',
            'gender',
        ],

        'materials' => [
            'matnr',
            'maktx',
            'mtart',
        ],

        'professions' => [
            'code',
            'title',
        ],

        'hierarchies' => [
            'code',
            'description',
            'datahub_hierarchy_id',
        ],

        'contract-types' => [
            'uri',
            'title',
        ],

        'contracts' => [
            'pid',
            'datahub_employee_id',
            'datahub_department_id',
            'datahub_profession_id',
            'datahub_contract_type_id',
            'circuit_id',
            'valid_from',
            'is_active',
            'is_primary',
        ],

        'locations' => [
            'title',
            'is_active',
        ],

        'departments' => [
            'code',
            'title',
            'datahub_hierarchy_id',
        ],

        'employee-circuits' => [
            'code',
        ],
    ],
];
