<?php

return [
    'create_heading' => 'Vytvoriť skupinu podzákazky',
    'list_heading' => 'Skupiny podzákazok',
    'update_heading' => 'Upraviť skupinu podzákazky: :title',
    'components' => [
        'picker' => [
            'label' => 'Skupina podzákazky',
            'create_heading' => 'Vytvoriť skupinu podzákazky',
            'update_heading' => 'Upraviť skupinu podzákazky: :title',
        ]
    ],
    'form' => [
        'fields' => [
            'code' => [
                'label' => 'Kód',
                'hint' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
            ],
            'title' => 'Názov',
            'parent' => 'Patrí pod',
        ],
    ],
    'table' => [
        'heading' => 'Skupiny podzákazok',
        'empty_state_heading' => 'Žiadne skupiny podzákazok na zobrazenie',
        'row_groups' => [],
        'columns' => [
            'code' => [
                'label' => 'Kód',
                'tooltip' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
            ],
            'title' => 'Názov',
            'parent' => 'Patrí pod',
        ]
    ],
    'navigation' => [
        'label' => 'Skupiny podzákazok',
        'group' => 'Zákazky',
    ],
    'resource' => [
        'model_label' => 'Skupina podzákazky',
        'plural_model_label' => 'Skupiny podzákazok',
    ],
];
