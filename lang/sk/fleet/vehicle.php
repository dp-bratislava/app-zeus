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
        'empty_state_heading' => 'Žiadne skupint vozidiel na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'code' => ['label' => 'Kód'],
            'title' => ['label' => 'Názov'],
            'description' => ['label' => 'Popis'],
        ]
    ],
    'navigation' => 'Skupiny vozidiel',
    'resource' => [
        'model_label' => 'Skupina vozidiel',
        'models_label' => 'Skupiny vozidiel',
    ],
    'states' => [
        'in-service' => 'V prevádzke',
        'discarded' => 'Vyradené',
    ]    
];