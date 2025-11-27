<?php

return [
    'create_heading' => 'Vytvoriť zákazku',
    'list_heading' => 'Zákazky',
    'update_heading' => 'Upraviť zákazku: :title',
    'form' => [
        'fields' => [
            'date' => 'Dátum',
            'title' => 'Názov',
            'description' => 'Popis',
            'source' => 'Miesto výskytu',
            'group' => 'Typ zákazky',
            'subject' => 'Vozidlo',
            'assigned_to' => 'Technická prevádzka',
            'department' => 'Stredisko',
        ],
        'sections' => [
            'ticket' => 'Zákazka',
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
        'row_groups' => [],
        'columns' => [
            'id' => 'ID',
            'date' => 'Dátum',
            'title' => 'Názov',
            'description' => 'Popis',
            'state' => 'Stav',
            'subject' => 'Vozidlo',
            'assigned_to' => [
                'label' => 'TP',
                'tooltip' => 'Technická prevádzka',
            ],
            'department' => 'Stredisko',
            'group' => 'Typ zákazky',
            'activities' => [
                'label' => 'Normy',
                'tooltip' => 'Predpokladané trvanie/skutočné trvanie'
            ],
            'source' => 'Miesto výskytu',
            'parent' => 'Patrí pod',
            'total_expenses' => 'Náklady'
        ],
        'filters' => [
            'date' => 'Dátum',
            'state' => 'Stav',
            'source' => 'Miesto výskytu',
            'subject' => 'Vozidlo',
            'assigned_to' => 'Technická prevádzka',
        ],
    ],
    'relation_manager' => [
        'ticket_items' => [
            'table' => [
                'heading' => 'Podzákazky',
                'empty_state_heading' => 'Žiadne podzákazky na zobrazenie',
                'empty_state_description' => 'Pre pokračovanie vytvorte podzákazku',
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
