<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť skupinu zákazky',
        'update_heading' => 'Upraviť skupinu zákazky: :title',
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
        'heading' => 'Zakázky',
        'empty_state_heading' => 'Žiadne skupiny zákazok na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'code' => ['label' => 'Kód'],
            'title' => ['label' => 'Názov'],
            'description' => ['label' => 'Popis'],
        ]
    ],
    'navigation' => [
        'label' => 'Skupiny zákazok',
        'group' => 'Zákazky',
    ],
    'resource' => [
        'model_label' => 'Skupina zákazka',
        'plural_model_label' => 'Skupiny zákazok',
    ],
];