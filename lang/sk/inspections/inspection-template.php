<?php

return [
    'components' => [
        'picker' => [
            'label' => 'Šablóna kontroly',
            'create_heading' => 'Vytvoriť šablónu kontroly',
            'update_heading' => 'Upraviť šablónu kontroly: :title',
        ],
    ],    
    'form' => [
        'create_heading' => 'Vytvoriť šablónu kontroly',
        'update_heading' => 'Upraviť šablónu kontroly: :title',
        'fields' => [
            'code' => ['label' => 'Kód'],
            'title' => ['label' => 'Názov'],
            'description' => ['label' => 'Popis'],
            'is_periodic' => ['label' => 'Cyklická'],
            'treshold_distance' => [
                'label' => 'Interval km',
                'hint' => 'počet km',
            ],
            'first_advance_distance' => [
                'label' => 'Prvý predstih km',
                'hint' => 'počet km (nie stav)',
            ],
            'second_advance_distance' => [
                'label' => 'Druhý predstih km',
                'hint' => 'počet km (nie stav)',
            ],
            'treshold_time' => [
                'label' => 'Interval',
                'hint' => 'mesiace',
            ],
            'first_advance_time' => [
                'label' => 'Prvý predstih',
                'hint' => 'dni',
            ],
            'second_advance_time' => [
                'label' => 'Druhý predstih',
                'hint' => 'dni',
            ],
            'groups' => ['label' => 'Typ údržby'],
            'templatables' => ['label' => 'Predmety kontroly'],
        ],
    ],
    'table' => [
        'heading' => 'Typy kontrol',
        'description' => 'Typy kontrol',
        'empty_state_heading' => 'Žiadne typy kontrol na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'code' => ['label' => 'Kód'],
            'title' => ['label' => 'Názov'],
            'description' => ['label' => 'Popis'],
            'is_periodic' => ['label' => 'Cyklická'],
            'treshold_distance' => [
                'label' => 'Interval km',
                'tooltip' => 'počet km',
            ],
            'first_advance_distance' => [
                'label' => 'Prvý predstih km',
                'tooltip' => 'počet km (nie stav)',
            ],
            'second_advance_distance' => [
                'label' => 'Druhý predstih km',
                'tooltip' => 'počet km (nie stav)',
            ],
            'treshold_time' => [
                'label' => 'Interval',
                'tooltip' => 'mesiace',
            ],
            'first_advance_time' => [
                'label' => 'Prvý predstih',
                'tooltip' => 'dni',
            ],
            'second_advance_time' => [
                'label' => 'Druhý predstih',
                'tooltip' => 'dni',
            ],
            'groups' => ['label' => 'Typ údržby'],
        ]
    ],
    'navigation' => [
        'label' => 'Typy kontrol',
        'group' => 'Kontroly',
    ],
    'resource' => [
        'model_label' => 'Typy kontrola',
        'plural_model_label' => 'Typy kontrol',
    ],
];