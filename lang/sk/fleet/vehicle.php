<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť vozidlo',
        'update_heading' => 'Upraviť vozidlo: :title',
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
        ],
    ],
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
    ]     
];