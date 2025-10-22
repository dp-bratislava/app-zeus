<?php

return [
    'form' => [
        'create_heading' => 'Denné ošetrenie',
        'update_heading' => 'Upraviť denné ošetrenie: :title',
        'fields' => [
            'code' => [
                'label' => 'Kód',
                'helper' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
                'tooltip' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
            ],
            'date' => 'Dátum',
            'vehicles' => 'Vozidlá',
            'template' => 'Typ ošetrenia',
            'contracts' => 'Zamestnanci',
            'activity-templates' => 'Činnosti / normy',
        ],
        'tabs' => [
            'activity-templates' => 'Činnosti / normy',
            'materials' => 'Materiál',
            'services' => 'Služby',
        ]
    ],
    'table' => [
        'heading' => 'Kontroly',
        'empty_state_heading' => 'Žiadne nadchádzajúce kontroly na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'id' => ['label' => 'ID'],
            'date' => ['label' => 'Dátum'],
            'template' => ['label' => 'Typ ošetrenia'],
            'state' => ['label' => 'Stav'],
            'subject' => ['label' => 'Vozidlo'],
            'note' => ['label' => 'Poznámka'],
            'maintenance_group' => ['label' => 'Prevádzka'],
            'contracts' => ['label' => 'Vykonal'],
            'total_time' => ['label' => 'Celkový čas']
        ],
        'actions' => [
            'create_ticket' => 'Vyvoriť zákazku',
        ]
    ],
    'navigation' => [
        'label' => 'Denné ošetrenie',
        'group' => 'Kontroly',
    ],
    'resource' => [
        'model_label' => 'Denné ošetrenie',
        'plural_model_label' => 'Denné ošetrenie',
    ],
];