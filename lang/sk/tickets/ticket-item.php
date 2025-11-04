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
            // 'title' => 'Názov',
            'title' => 'Detail poruchy',
            'group' => 'Detail poruchy',
            'description' => 'Popis',
            'source' => 'Miesto výskytu',
            'subject' => 'Vozidlo',
            'department' => 'Stredisko',
            'activities' => [
                'date' => 'Dátum',
                'template' => 'norma',
                'work_log' => [
                    'title' => 'Pracovné výkony',
                    'date' => 'Dátum',
                    'time_from' => 'Od',
                    'time_to' => 'Do',
                    'contract' => 'Úväzok',
                ]
            ],
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
            // 'title' => ['label' => 'Názov'],
            'title' => ['label' => 'Detail poruchy'],
            'group' => ['label' => 'Detail poruchy'],
            'description' => ['label' => 'Popis'],
            'state' => ['label' => 'Stav'],
            'subject' => ['label' => 'Vozidlo'],
            'department' => ['label' => 'Stredisko'],
            'activities' => [
                'label' => 'Normy',
                'tooltip' => 'Predpokladané trvanie/skutočné trvanie'
            ],
            'source' => ['label' => 'Miesto výskytu'],
            'ticket' => ['label' => 'Patrí pod'],
        ]
    ],
    'navigation' => [
        'label' => 'Podzákazky',
        'group' => 'Zákazky',
    ],
    'resource' => [
        'model_label' => 'Podzákazka',
        'plural_model_label' => 'Podzákazky',
    ],
    'states' => [
        'created' => 'Nová',
        'cancelled' => 'Zrušená',
        'closed' => 'Uzavretá',
        'in-progress' => 'V riešení',
    ]
];