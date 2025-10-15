<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť dopravnú prevádzku',
        'update_heading' => 'Upraviť dopravnú prevádzku: :title',
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
        'heading' => 'Dopravnú prevádzky',
        'empty_state_heading' => 'Žiadne dopravné prevádzky na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'code' => 'Kód',
            'title' => 'Názov',
            'description' => 'Popis',
        ]
    ],
    'navigation' => [
        'label' => 'Dopravné prevádzky',
        'group' => 'Flotila',
    ],
    'resource' => [
        'model_label' => 'Dopravná prevádzka',
        'plural_model_label' => 'Dopravné prevádzky',
    ],
];