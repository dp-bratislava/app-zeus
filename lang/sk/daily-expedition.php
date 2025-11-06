<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť štrkáč',
        'update_heading' => 'Upraviť štrkáč: :title',
        'fields' => [
            'date' => 'Dátum',
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
