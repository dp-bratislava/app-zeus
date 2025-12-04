<?php

return [
    'list_heading' => 'Správka',
    'table' => [
        'heading' => 'Správka',
        'description' => 'ZATIAĽ: Vozy čo majú v štrkáči ako posledné, že sú odstavené',
        'row_groups' => [],
        'columns' => [
            'code' => 'Kód',
            'vin' => 'VIN',
            'model' => 'Model',
            'type' => 'Typ',
            'licence_plate' => 'EČV',
            'groups' => 'Skupiny',
            'department' => 'SAP Stredisko',
            'state' => 'Stav',
            'note' => 'Pozn.',
            'rds' => 'RDS',
            'ocl' => 'OCL',
            'dispatch_group' => 'Dopravná prevádzka',
            'maintenance_group' => [
                'label' => 'TP',
                'tooltip' => 'Technciká prevádzka',
            ],
            'date_from' => [
                'label' => 'V správke od',
                'tooltip' => 'Dátum zaradenia do správky',
            ],
            'days_out_of_service' => [
                'label' => 'V správke dní',
                'tooltip' => '',
            ],
            'closest_inspections' => [
                'label' => 'STK/EK',
                'tooltip' => 'Najbližšie plánované kontroly',
            ],
        ],
        'filters' => [
            'model' => 'Model',
        ],
    ],
    'navigation' => [
        'label' => 'Správka',
        'group' => 'Reporty',
    ],
    'resource' => [
        'model_label' => 'Správka',
        'plural_model_label' => 'Správka',
    ],
    'states' => [
        'in-service' => 'V prevádzke',
        'under-repair' => 'V oprave',
        'discarded' => 'Vyradené',
    ]
];
