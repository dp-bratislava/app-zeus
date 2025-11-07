<?php

return [
    'form' => [
        'create_heading' => 'Denné ošetrenie',
        'update_heading' => 'Upraviť denné ošetrenie: :title',
        'fields' => [
            'date' => 'Dátum',
            'vehicles' => 'Vozidlá',
            'template' => 'Typ ošetrenia',
            'contracts' => 'Zamestnanci',
            'activity-templates' => 'Činnosti / normy',
        ],
        'sections' => [
            'vehicles' => 'Vozidlá',
            'contracts' => 'Zamestnanci',
            'activity-templates' => 'Činnosti / normy',
       ]
    ],
    'table' => [
        'heading' => 'Kontroly',
        'empty_state_heading' => 'Žiadne denné ošetrenie na zobrazenie',
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