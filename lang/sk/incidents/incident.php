<?php

return [
    'create_heading' => 'Vytvoriť udalosť',
    'list_heading' => 'Udalosti',
    'update_heading' => 'Upraviť udalosť: :title',
    'form' => [
        'fields' => [
            'date' => 'Dátum',
            'description' => 'Popis',
            'type' => 'Typ',
            'subject' => 'Vozidlo',
        ],
    ],
    'table' => [
        'heading' => 'Udalosti',
        'description' => 'Nahlasované dispečingom. Ak ešte neexistuje zákazka, dá sa vytvoriť zataiľ manuálne, časom by sa mala automaticky',
        'empty_state_heading' => 'Žiadne udalosti na zobrazenie',
        'columns' => [
            'id' => 'ID',
            'date' => 'Dátum',
            'description' => 'Popis',
            'type' => 'Typ',
            'state' => 'Stav',
            'subject' => 'Vozidlo',
        ],
        'filters' => [
            'date' => 'Dátum',
            'description' => 'Popis',
            'type' => 'Typ',
            'state' => 'Stav',
            'subject' => 'Vozidlo',
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