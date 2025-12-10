<?php

return [
    'create_heading' => 'Vytvoriť model vozidla',
    'update_heading' => 'Upraviť model vozidila: :title',
    'form' => [
        'fields' => [
            'brand' => ['label' => 'Výrobca'],
            'title' => ['label' => 'Názov'],
            'year' => ['label' => 'Rokčník'],
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
            'year' => ['label' => 'Rokčník'],
            'length' => ['label' => 'Dĺžka m'],
            'type' => ['label' => 'Typ'],
            'fuel_consumption' => ['label' => 'Spotreba'],
        ],
        'filters' => [
            'brand' => ['label' => 'Výrobca'],
            'title' => ['label' => 'Názov'],
            'year' => ['label' => 'Rokčník'],
            'length' => ['label' => 'Dĺžka m'],
            'type' => ['label' => 'Typ'],
        ],
        'actions' => [
            'bulk_set_brand' => 'Priradiť výrobcu',
            'bulk_set_vehicle_type' => 'Priradiť typ',
        ]
    ],
    'navigation' => [
        'label' => 'Modely vozidiel',
        'group' => 'Flotila',
    ],
    'resource' => [
        'model_label' => 'Model vozidla',
        'plural_model_label' => 'Modely vozidiel',
    ],
];