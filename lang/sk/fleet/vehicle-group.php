<?php

return [
    'create_heading' => 'Vytvoriť skupinu vozidiel',
    'update_heading' => 'Upraviť skupinu vozidiel: :title',
    'form' => [
        'fields' => [
            'code' => [
                'label' => 'Kód',
                'helper' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
                'tooltip' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
            ],
            'title' => 'Názov',
            'description' => 'Popis',
            'vehicles' => 'Vozidlá',
        ],
    ],
    'table' => [
        'heading' => 'Skupiny vozidiel',
        'empty_state_heading' => 'Žiadne skupiny vozidiel na zobrazenie',
        'columns' => [
            'code' => 'Kód',
            'title' => 'Názov',
            'description' => 'Popis',
            'vehicles' => 'Vozidlá',
        ]
    ],
    'navigation' => [
        'label' => 'Skupiny vozidiel',
        'group' => 'Flotila',
    ],
    'resource' => [
        'model_label' => 'Skupina vozidiel',
        'plural_model_label' => 'Skupiny vozidiel',
    ],
];