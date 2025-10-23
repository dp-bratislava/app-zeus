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
            'is_periodic' => ['label' => 'Cyklická'],
            'interval_distance' => ['label' => 'inter km'],
            'first_advance_distance' => ['label' => '1 pred km'],
            'second_advance_distance' => ['label' => '2 pred km'],
            'interval_time' => ['label' => 'inter dni'],
            'first_advance_time' => ['label' => '1 pred dni'],
            'second_advance_time' => ['label' => '2 pred dni'],
            'groups' => ['label' => 'Skupiny'],
        ]
    ],
    'navigation' => [
        'label' => 'Šablóny Kontrol',
        'group' => 'Kontroly',
    ],
    'resource' => [
        'model_label' => 'Šablóny kontrola',
        'plural_model_label' => 'Šablóny kontrol',
    ],
];