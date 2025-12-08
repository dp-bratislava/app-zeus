<?php

return [
    'create_heading' => 'Vytvoriť kontrolu',
    'list_heading' => 'Kontroly',
    'update_heading' => 'Upraviť kontrolu: :title',
    'form' => [
        'fields' => [
            'date' => 'Dátum',
            'template' => 'Typ kontroly',
            'subject' => 'Vozidlo',
            'description' => 'Popis',
        ],
    ],
    'table' => [
        'heading' => 'Kontroly',
        'empty_state_heading' => 'Žiadne kontroly na zobrazenie',
        'columns' => [
            'date' => 'Dátum',
            'subject' => 'Vozidlá',
            'template' => 'Typ ošetrenia',
            'state' => 'Stav',
            'finished_at' => 'Ukončená',
            'distance_traveled' => 'Stav km',
            'note' => 'Poznámka',
            'maintenance_group' => 'Prevádzka',
            'contracts' => 'Vykonal',
            'total_time' => 'Celkový čas'
        ],
        'actions' => [
            'create_ticket' => 'Vyvoriť zákazku',
        ],
        'filters' => [
            'date' => 'Dátum',
            'subject' => 'Vozidlá',
            'template' => 'Typ ošetrenia',
            'state' => 'Stav',
            'maintenance_group' => 'Prevádzka',
        ],

    ],
    'navigation' => [
        'label' => 'Kontroly',
        'group' => 'Kontroly',
    ],
    'resource' => [
        'model_label' => 'Kontrola',
        'plural_model_label' => 'Kontroly',
    ],
    'states' => [
        'upcoming' => 'Nadchádzajúca',
        'overdue' => 'Po termíne',
        'in-progress' => 'Prebieha',
    ]
];