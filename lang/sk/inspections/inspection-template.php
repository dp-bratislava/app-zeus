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
            'is_periodic' => ['label' => 'Cyklická'],
            'interval_distance' => ['label' => 'inter km'],
            'first_advance_distance' => ['label' => '1 pred km'],
            'second_advance_distance' => ['label' => '2 pred km'],
            'interval_time' => ['label' => 'inter dni'],
            'first_advance_time' => ['label' => '1 pred dni'],
            'second_advance_time' => ['label' => '2 pred dni'],
            'groups' => ['label' => 'Skupiny'],
        ],
        'tabs' => [
            'activities' => 'Činnosti / normy',
            'materials' => 'Materiál',
            'services' => 'Služby',
        ]
    ],
    'table' => [
        'heading' => 'šablóny kontrol',
        'empty_state_heading' => 'Žiadne šablóny kontrol na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'code' => ['label' => 'Kód'],
            'title' => ['label' => 'Názov'],
            'is_periodic' => ['label' => 'Cyklická'],
            'interval_distance' => ['label' => 'inter km'],
            'first_advance_distance' => ['label' => '1 pred km'],
            'second_advance_distance' => ['label' => '2 pred km'],
            'interval_time' => ['label' => 'inter dni'],
            'first_advance_time' => ['label' => '1 pred dni'],
            'second_advance_time' => ['label' => '2 pred dni'],
            'groups' => ['label' => 'Skupiny'],
        ]
    ],
    'navigation' => [
        'label' => 'Šablóny kontrol',
        'group' => 'Kontroly',
    ],
    'resource' => [
        'model_label' => 'Šablóny kontrola',
        'plural_model_label' => 'Šablóny kontrol',
    ],
];