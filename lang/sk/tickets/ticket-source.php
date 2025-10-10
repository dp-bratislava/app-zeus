<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť miesto výskytu',
        'update_heading' => 'Upraviť miesto výskytu: :title',
        'fields' => [
            'code' => [
                'label' => 'Kód',
                'helper' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
                'tooltip' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
            ],
            'title' => 'Názov',
        ],
    ],
    'table' => [
        'heading' => 'Zakázky',
        'empty_state_heading' => 'Žiadne miesta výskytu na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'code' => ['label' => 'Kód'],
            'title' => ['label' => 'Názov'],
        ]
    ],
    'navigation' => [
        'label' => 'Miesta výskytu',
        'group' => 'Zákazky',
    ],
    'resource' => [
        'model_label' => 'Miesto výskytu',
        'plural_model_label' => 'Miesta výskytu',
    ],
];