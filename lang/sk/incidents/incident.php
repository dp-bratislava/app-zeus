<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť udalosť',
        'update_heading' => 'Upraviť udalosť: :title',
        'fields' => [
            'date' => 'Dátum',
            'description' => 'Popis',
            'type' => 'Typ',
        ],
        'tabs' => [
            'activities' => 'Činnosti / normy',
            'materials' => 'Materiál',
            'services' => 'Služby',
        ]
    ],
    'table' => [
        'heading' => 'Udalosti',
        'empty_state_heading' => 'Žiadne udalosti na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'id' => ['label' => 'ID'],
            'date' => ['label' => 'Dátum'],
            'description' => ['label' => 'Popis'],
            'type' => ['label' => 'Typ'],
            'state' => ['label' => 'Stav'],
            'subject' => ['label' => 'Vozidlo'],
        ],
        'actions' => [
            'create_ticket' => 'Vyvoriť zákazku',
        ]
    ],
    'navigation' => [
        'label' => 'Udalosti',
        'group' => 'Udalosti',
    ],
    'resource' => [
        'model_label' => 'Udalosť',
        'plural_model_label' => 'Udalosti',
    ],
    'states' => [
        'created' => 'Nová',
        'closed' => 'Uzavretá',
    ]
];