<?php

return [
    'create_heading' => 'Vytvoriť typ vozidla',
    'update_heading' => 'Upraviť typ vozidila: :title',
    'components' => [
        'picker' => [
            'label' => 'Typ vozidla',
            'create_heading' => 'Vytvoriť typ vozidla',
            'update_heading' => 'Upraviť typ vozidila: :title',
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
            'models' => 'Modely vozidiel',
        ],
    ],
    'table' => [
        'heading' => 'Typy vozidiel',
        'empty_state_heading' => 'Žiadne typy vozidiel na zobrazenie',
        'row_groups' => [],
        'columns' => [
            'code' => 'Kód',
            'title' => 'Názov',
            'models' => 'Modely vozidiel',
        ]
    ],
    'navigation' => [
        'label' => 'Typy vozidiel',
        'group' => 'Flotila',
    ],
    'resource' => [
        'model_label' => 'Typ vozidla',
        'plural_model_label' => 'Typy vozidiel',
    ],
];
