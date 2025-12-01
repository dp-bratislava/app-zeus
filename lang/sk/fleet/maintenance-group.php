<?php

return [
    'create_heading' => 'Vytvoriť technickú prevádzku',
    'update_heading' => 'Upraviť technickú prevádzku: :title',
    'components' => [
        'picker' => [
            'label' => 'Technická prevádzka',
            'create_heading' => 'Vytvoriť technickú prevádzku',
            'update_heading' => 'Upraviť technickú prevádzku: :title',
        ],
    ],     
    'form' => [
        'fields' => [
            'code' => [
                'label' => 'Kód',
                'helper' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
                'tooltip' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
            ],
            'title' => 'Názov',
            'description' => 'Popis',
            'color' => 'Farba',
            'vehicle_type' => 'Typ',
            'vehicles' => 'Vozidlá',
        ],
    ],
    'table' => [
        'heading' => 'Technické prevádzky',
        'empty_state_heading' => 'Žiadne technické prevádzky na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'code' => 'Kód',
            'title' => 'Názov',
            'description' => 'Popis',
            'color' => 'Farba',
            'vehicle_type' => 'Typ',
            'vehicles' => 'Vozidlá',
        ]
    ],
    'navigation' => [
        'label' => 'Technické prevádzky',
        'group' => 'Flotila',
    ],
    'resource' => [
        'model_label' => 'Technická prevádzka',
        'plural_model_label' => 'Technické prevádzky',
    ],
];