<?php

use Dpb\DatahubSync\Models\Attendance\{
    Absence,
    AbsenceType,
    Attendance,
    Group,
    Shift
};
use Dpb\DatahubSync\Models\{
    ContractType,
    Department,
    Employee,
    EmployeeCircuit,
    EmployeeContract,
    Hierarchy,
    Location,
    Material,
    Profession
};

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

        Location::class => ['enabled' => false, 'columns' => '*'],
        Hierarchy::class => ['enabled' => true, 'columns' => '*'],
        Material::class => ['enabled' => false, 'columns' => ['code', 'title', 'measure_unit', 'type']],
        ContractType::class => ['enabled' => true, 'columns' => '*'],
        Department::class => ['enabled' => true, 'columns' => '*'],
        EmployeeCircuit::class => ['enabled' => true, 'columns' => '*'],
        Employee::class => ['enabled' => true, 'columns' => '*'],
        Profession::class => ['enabled' => true, 'columns' => '*'],
        EmployeeContract::class => ['enabled' => true, 'columns' => '*'],

        /** Attendance */
        Group::class => ['enabled' => true, 'columns' => '*'],
        Shift::class => ['enabled' => true, 'columns' => '*'],
        AbsenceType::class => ['enabled' => true, 'columns' => '*'],
        Attendance::class => ['enabled' => false, 'columns' => '*'],
        Absence::class => ['enabled' => true, 'columns' => '*'],
    ],
];
