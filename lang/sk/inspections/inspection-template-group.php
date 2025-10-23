<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť kontrolu',
        'update_heading' => 'Upraviť kontrolu: :title',
        'fields' => [
            'code' => [
                'label' => 'Kód',
                'helper' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
                'tooltip' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
            ],
            'date' => 'Dátum',
            'title' => 'Názov',
            'description' => 'Popis',
            'source' => 'Miesto výskytu',
        ],
        'tabs' => [
            'activities' => 'Činnosti / normy',
            'materials' => 'Materiál',
            'services' => 'Služby',
        ]
    ],
    'table' => [
        'heading' => 'Zakázky',
        'empty_state_heading' => 'Žiadne kontroly na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'id' => ['label' => 'ID'],
            'code' => ['label' => 'Kód'],
            'title' => ['label' => 'Názov'],
            'description' => ['label' => 'Popis'],
        ]
    ],
    'navigation' => [
        'label' => 'Skupiny šablón',
        'group' => 'Kontroly',
    ],
    'resource' => [
        'model_label' => 'Skupina šablón',
        'plural_model_label' => 'Skupiny šablón',
    ],
];