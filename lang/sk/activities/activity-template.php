<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť normu',
        'update_heading' => 'Upraviť normu: :title',
        'sections' => [
            'subjects' => [
                'label' => 'Modely vozidiel',
                'description' => 'Norma platí pre tieto modely vozidiel',
            ],
        ],
        'fields' => [
            'code' => [
                'label' => 'Kód',
                'helper' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
                'tooltip' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
            ],
            'date' => 'Dátum',
            'title' => 'Názov',
            'duration' => [
                'label' => 'Trvanie min',
                'helper' => 'Hodnota uvádzná v minútach',
        ],
            'man_minutes' => 'Človekominúty',
            'is_divisible' => 'Deliteľná',
            'is_catalogised' => 'Katalogizovaná',
            'people' => 'Počet ľudí',
            'unit_price' => 'Jedn. sadzba',
            'subject' => 'Model vozidla'
        ],
    ],
    'table' => [
        'heading' => 'Normy',
        'empty_state_heading' => 'Žiadne normy na zobrazenie',
        'row_groups' => [],
        'columns' => [
            'id' => ['label' => 'ID'],
            'code' => ['label' => 'Kód'],
            'title' => ['label' => 'Názov'],
            'duration' => ['label' => 'Trvanie min'],
            'man_minutes' => ['label' => 'Človekominúty'],
            'is_divisible' => ['label' => 'Deliteľná'],
            'is_catalogised' => ['label' => 'Katalogizovaná'],
            'people' => ['label' => 'Počet ľudí'],
            'unit_price' => ['label' => 'Jedn. sadzba'],
        ]
    ],
    'navigation' => [
        'label' => 'Normy',
        'group' => 'Normy',
    ],
    'page' => [
        'title'=> 'Norma'
    ],
    'resource' => [
        'model_label' => 'Norma',
        'plural_model_label' => 'Normy',
    ],
];
