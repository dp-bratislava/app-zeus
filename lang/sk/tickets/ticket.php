<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť zákazku',
        'update_heading' => 'Upraviť zákazku: :title',
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
        'tabs' => [
            'activities' => 'Práca',
            'materials' => 'Materiál',
            'services' => 'Služby',
        ]
    ],
    'table' => [
        'heading' => 'Zakázky',
        'empty_state_heading' => 'Žiadne zákazky na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'id' => ['label' => 'ID'],
            'date' => ['label' => 'Dátum'],
            'title' => ['label' => 'Názov'],
            'description' => ['label' => 'Popis'],
            'state' => ['label' => 'Stav'],
            'subject' => ['label' => 'Vozidlo'],
            'department' => ['label' => 'Stredisko'],
            'activities' => [
                'label' => 'Normy',
                'tooltip' => 'Predpokladané trvanie/skutočné trvanie'
            ],
            'source' => ['label' => 'Miesto výskytu'],
            'parent' => ['label' => 'Patrí pod'],
        ]
    ],
    'relation_manager' => [
        'ticket_items' => [
            'table' => [
                'heading' => 'Podzákazky'
            ]
        ]
    ],
    'navigation' => [
        'label' => 'Zákazky',
        'group' => 'Zákazky',
    ],
    'resource' => [
        'model_label' => 'Zákazka',
        'plural_model_label' => 'Zákazky',
    ],
    'states' => [
        'created' => 'Nová',
        'cancelled' => 'Zrušená',
        'closed' => 'Uzavretá',
        'in-progress' => 'V riešení',
    ]
];