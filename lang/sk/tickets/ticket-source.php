<?php

return [
    'create_heading' => 'Vytvoriť miesto výskytu',
    'list_heading' => 'Miesta výskytu',
    'update_heading' => 'Upraviť miesto výskytu: :title',
    'form' => [
        'fields' => [
            'code' => [
                'label' => 'Kód',
                'hint' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
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
            'code' => [
                'label' => 'Kód',
                'tooltip' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
            ],
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