<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť hlásenie',
        'update_heading' => 'Upraviť hlásenie: :title',
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
            'subject' => 'Vozidlo',
            'department' => 'Stredisko',
        ],
    ],
    'table' => [
        'heading' => 'Dispečerské hlásenia',
        'empty_state_heading' => 'Žiadne dispečerské hlásenia na zobrazenie',
        'columns' => [
            'id' => ['label' => 'ID'],
            'date' => ['label' => 'Dátum'],
            'title' => ['label' => 'Názov'],
            'description' => ['label' => 'Popis'],
            'state' => ['label' => 'Stav'],
            'subject' => ['label' => 'Vozidlo'],
            'author' => ['label' => 'Nahlásil'],
        ],
        'actions' => [
            'create_ticket' => 'Vyvoriť zákazku',
        ]
    ],
    'navigation' => [
        'label' => 'Dispečerské hlásenia',
        'group' => 'Zákazky',
    ],
    'resource' => [
        'model_label' => 'Dispečerské hlásenie',
        'plural_model_label' => 'Dispečerské hlásenia',
    ],
    'states' => [
        'created' => 'Nová',
        'cancelled' => 'Zrušená',
        'closed' => 'Uzavretá',
        'in-progress' => 'V riešení',
    ]
];
