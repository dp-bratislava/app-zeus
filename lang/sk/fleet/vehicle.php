<?php

return [
    'create_heading' => 'Vytvoriť vozidlo',
    'list_heading' => 'Vozidlá',
    'update_heading' => 'Upraviť vozidlo: :title',
    'form' => [
        'fields' => [
            'code' => [
                'label' => 'Kód',
                'helper' => 'Štvormiestny kód vozidla',
                'tooltip' => 'Štvormiestny kód vozidla',
            ],
            'licence_plate' => 'EČV',
            'dispatch_group' => 'Dopravná prevádzka',
            'maintenance_group' => 'Technciká prevádzka',
            'vin' => 'VIN',
            'model' => 'Model',
            'department' => 'Stredisko',
            'groups' => 'Skupiny',
            'construction_year' => 'Rok výroby',
            'warranty_initial_date' => [
                'label' => 'Záruka platí od',
                'hint' => '',
            ],            
            'warranty_months' => [
                'label' => 'Záruka',
                'hint' => 'V mesiacoch',
            ],
            'warranty_initial_km' => [
                'label' => 'Záruka platí od km',
                'hint' => '',
            ],            
            'warranty_km' => [
                'label' => 'Záruka',
                'hint' => 'V km',
            ]            
        ],
    ],
    'table' => [
        'heading' => 'Vozidlá',
        'empty_state_heading' => 'Žiadne vozidlá na zobrazenie',
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
            'total_distance' => '',
            'distance_since_inspection' => '',
            'under_warranty' => [
                'label' => 'Zár',
                'tooltip' => 'V záruke',
            ],
            'maintenance_group' => [
                'label' => 'TP',
                'tooltip' => 'Technciká prevádzka'
            ],
        ]
    ],
    'navigation' => [
        'label' => 'Vozidlá',
        'group' => 'Flotila',
    ],
    'resource' => [
        'model_label' => 'Vozidlo',
        'plural_model_label' => 'Vozidlá',
    ],
    'states' => [
        'in-service' => 'V prevádzke',
        'under-repair' => 'Prebieha oprava',
        'discarded' => 'Vyradené',
        'warranty-repair' => 'Záručná oprava',
        'warranty-claim' => 'Reklamácia',
        'missing-parts' => 'Čaká na náhr. diely',
        'waiting-for-repair-spot' => 'Čaká na voľné pracovisko',
        'waiting-for-insurance' => 'Čaká na poisťovňu',
    ]     
];