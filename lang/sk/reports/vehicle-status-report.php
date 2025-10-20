<?php

return [
    'table' => [
        'heading' => 'Vozidlá',
        'empty_state_heading' => 'Žiadne vozidlá na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'code' => 'Kód',
            'vin' => 'VIN',
            'model' => 'Model',
            'type' => 'Typ',
            'licence_plate' => 'EČV',
            'groups' => 'Skupiny',
            'department' => 'SAP Stredisko',
            'state' => 'Stav',
            'dispatch_group' => 'Dopravná prevádzka',
            'maintenance_group' => 'Technciká prevádzka',
        ]
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