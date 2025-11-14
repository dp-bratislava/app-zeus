<?php

return [
    'create_heading' => 'Vytvoriť skupinu zákazky',
    'list_heading' => 'Skupiny zákazkok',
    'update_heading' => 'Upraviť skupinu zákazky: :title',
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
        'heading' => 'Zakázky',
        'empty_state_heading' => 'Žiadne skupiny zákazok na zobrazenie',
        'row_groups' => [
        ],
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
        'label' => 'Skupiny zákazok',
        'group' => 'Zákazky',
    ],
    'resource' => [
        'model_label' => 'Skupina zákazka',
        'plural_model_label' => 'Skupiny zákazok',
    ],
];