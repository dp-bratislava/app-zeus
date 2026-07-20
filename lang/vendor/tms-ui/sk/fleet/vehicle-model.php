<?php

return [
    'create_heading' => 'Vytvoriť model vozidla',
    'list_heading' => 'Modely vozidiel',
    'update_heading' => 'Upraviť model vozidila: :title',
    'form' => [
        'fields' => [
            'brand' => ['label' => 'Výrobca'],
            'title' => ['label' => 'Názov'],
            'year' => ['label' => 'Ročník'],
            'type' => ['label' => 'Typ'],
        ],
        'tabs' => [
            'activity-templates' => 'Normy',
            'parameters' => 'Parametre',
            'vehicles' => 'Vozidlá',
        ]
    ],
    'table' => [
        'heading' => 'Modely vozidiel',
        'empty_state_heading' => 'Žiadne modely vozidiel na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'brand' => ['label' => 'Výrobca'],
            'title' => ['label' => 'Názov'],
            'year' => ['label' => 'Ročník'],
            'length' => ['label' => 'Dĺžka m'],
            'type' => ['label' => 'Typ'],
            'fuel_consumption' => ['label' => 'Spotreba'],
            'seats' => [
                'label' => 'Poč. sedadiel',
                'tooltip' => 'Celkový počet sedadiel',
            ],
        ],
        'filters' => [
            'brand' => 'Výrobca',
            'title' => 'Názov',
            'year' => 'Ročník',
            'length' => 'Dĺžka m',
            'type' => 'Typ',
        ],
        'actions' => [
            'bulk_set_brand' => 'Priradiť výrobcu',
            'bulk_set_vehicle_type' => 'Priradiť typ',
        ]
    ],
    'navigation' => [
        'label' => 'Modely vozidiel',
        'group' => 'Číselníky',
    ],
    'resource' => [
        'model_label' => 'Model vozidla',
        'plural_model_label' => 'Modely vozidiel',
    ],
];
