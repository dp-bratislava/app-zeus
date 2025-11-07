<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť skupinu vozidiel',
        'update_heading' => 'Upraviť skupinu vozidiel: :title',
        'fields' => [
            'code' => [
                'label' => 'Kód',
                'helper' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
                'tooltip' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
            ],
            'title' => 'Názov',
            'description' => 'Popis',
        ],
    ],
    'table' => [
        'heading' => 'Skupiny vozidiel',
        'empty_state_heading' => 'Žiadne skupiny vozidiel na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'code' => ['label' => 'Kód'],
            'title' => ['label' => 'Názov'],
            'description' => ['label' => 'Popis'],
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