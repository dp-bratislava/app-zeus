<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť normu',
        'update_heading' => 'Upraviť normu: :title',
        'fields' => [
            'code' => [
                'label' => 'Kód',
                'helper' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
                'tooltip' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
            ],
            'date' => 'Dátum',
            'title' => 'Názov',
            'duration' => 'Trvanie min',
            'man_minutes' => 'Človekominúty',
            'divisible' => 'Deliteľná',
            'catalogised' => 'Katalogizovaná',
            'people' => 'Počet ľudí',
            'unit_price' => 'Jedn. sadzba',
        ],
        'tabs' => [
            'activities' => 'Činnosti / normy',
            'materials' => 'Materiál',
            'services' => 'Služby',
        ]
    ],
    'table' => [
        'heading' => 'Zakázky',
        'empty_state_heading' => 'Žiadne normy na zobrazenie',
        'row_groups' => [],
        'columns' => [
            'id' => ['label' => 'ID'],
            'code' => ['label' => 'Kód'],
            'title' => ['label' => 'Názov'],
            'duration' => ['label' => 'Trvanie min'],
            'man_minutes' => ['label' => 'Človekominúty'],
            'divisible' => ['label' => 'Deliteľná'],
            'catalogised' => ['label' => 'Katalogizovaná'],
            'people' => ['label' => 'Počet ľudí'],
            'unit_price' => ['label' => 'Jedn. sadzba'],
        ]
    ],
    'navigation' => [
        'label' => 'Normy',
        'group' => 'Normy',
    ],
    'resource' => [
        'model_label' => 'Norma',
        'plural_model_label' => 'Normy',
    ],
];
