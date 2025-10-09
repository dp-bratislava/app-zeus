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
            'title' => 'Názov',
            'description' => 'Popis',
        ],
    ],
    'table' => [
        'heading' => 'Zakázky',
        'empty_state_heading' => 'Žiadne zákazky na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'date' => ['label' => 'Dátum'],
            'title' => ['label' => 'Názov'],
            'description' => ['label' => 'Popis'],
            'state' => ['label' => 'Stav'],
            'subject' => ['label' => 'Vozidlo'],
            'department' => ['label' => 'Stredisko'],
            'activities' => ['label' => 'Normy'],
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
        'in_progress' => 'V riešení',
    ]
];