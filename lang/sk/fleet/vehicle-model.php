<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť model vozidla',
        'update_heading' => 'Upraviť model vozidila: :title',
        'fields' => [
            'title' => ['label' => 'Názov'],
            'year' => ['label' => 'Rok výroby'],
            'type' => ['label' => 'Typ'],
        ],
    ],
    'table' => [
        'heading' => 'Modely vozidiel',
        'empty_state_heading' => 'Žiadne modely vozidiel na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'title' => ['label' => 'Názov'],
            'year' => ['label' => 'Rok výroby'],
            'length' => ['label' => 'Dĺžka m'],
            'type' => ['label' => 'Typ'],
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