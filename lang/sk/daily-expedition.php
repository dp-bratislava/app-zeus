<?php

return [
    'create_heading' => 'Vytvoriť štrkáč',
    'list_heading' => 'Vytvoriť štrkáč',
    'update_heading' => 'Upraviť štrkáč: :title',
    'form' => [
        'fields' => [
            'date' => 'Dátum',
            'vehicles_repeater' => 'Vozidlá',
            'vehicles' => [
                'state' => 'Stav',
                'service' => 'Služba',
                'note' => 'Poznámka',
                'vehicle' => 'Vozidlo',
            ],
        ],
    ],
    'table' => [
        'heading' => 'Štrkáč',
        'empty_state_heading' => 'Žiadny štrkáč na zobrazenie',
        'empty_state_description' => 'Žiadny štrkáč na zobrazenie',
        'columns' => [
            'date' => 'Dátum',
            'state' => 'Stav',
            'service' => 'Služba',
            'note' => 'Poznámka',
            'vehicle' => 'Vozidlo',
        ],
        'actions' => [
            'create_ticket' => 'Vyvoriť štrkáč',
        ]
    ],
    'navigation' => [
        'label' => 'Štrkáč',
        'group' => 'Štrkáč',
    ],
    'resource' => [
        'model_label' => 'Štrkáč',
        'plural_model_label' => 'Štrkáč',
    ],
    'states' => [
        'created' => 'Nová',
        'cancelled' => 'Zrušená',
        'closed' => 'Uzavretá',
        'in-progress' => 'V riešení',
    ]
];
