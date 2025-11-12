<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť podzákazku',
        'update_heading' => 'Upraviť podzákazku: :code :subject',
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
            'ticket' => 'Zákazka',
            'state' => 'Stav',
            'assigned_to' => 'Technická prevádzka',
            'activities' => [
                'title' => 'Normy',
                'date' => 'Dátum',
                'template' => 'Norma',
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
            'history' => 'História',
            'comments' => 'Komentáre',
        ]
    ],
    'table' => [
        'heading' => 'Podzakázky',
        'empty_state_heading' => 'Žiadne podzákazky na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'id' => ['label' => 'ID'],
            'code' => ['label' => 'Kód'],
            'date' => ['label' => 'Dátum'],
            // 'title' => ['label' => 'Názov'],
            'title' => ['label' => 'Detail poruchy'],
            'group' => ['label' => 'Detail poruchy'],
            'description' => ['label' => 'Popis'],
            'state' => ['label' => 'Stav'],
            'subject' => ['label' => 'Vozidlo'],
            'ticket' => ['label' => 'Zákazka'],
            'assigned_to' => [
                'label' => 'TP',
                'tooltip' => 'Technická prevádzka'
            ],
            'activities' => [
                'label' => 'Normy',
                'tooltip' => 'Predpokladané trvanie/skutočné trvanie'
            ],
            'source' => ['label' => 'Miesto výskytu'],
            'expenses' => 'Náklady'
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
        'awaiting-parts' => 'Čaká na ND',
    ]
];