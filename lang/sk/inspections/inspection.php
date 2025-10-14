<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť kontrolu',
        'update_heading' => 'Upraviť kontrolu: :title',
        'fields' => [
            'code' => [
                'label' => 'Kód',
                'helper' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
                'tooltip' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
            ],
            'date' => 'Dátum',
            'title' => 'Názov',
            'description' => 'Popis',
            'source' => 'Miesto výskytu',
        ],
        'tabs' => [
            'activities' => 'Činnosti / normy',
            'materials' => 'Materiál',
            'services' => 'Služby',
        ]
    ],
    'table' => [
        'heading' => 'Zakázky',
        'empty_state_heading' => 'Žiadne kontroly na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'id' => ['label' => 'ID'],
            'date' => ['label' => 'Dátum'],
            'template' => ['label' => 'Typ kontroly'],
            'state' => ['label' => 'Stav'],
            'subject' => ['label' => 'Vozidlo'],
            'note' => ['label' => 'Poznámka'],
            'maintenance_group' => ['label' => 'Prevádzka'],
            'finished_at' => ['label' => 'Ukončená'],
            'distance_traveled' => ['label' => 'Stav km'],
        ]
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