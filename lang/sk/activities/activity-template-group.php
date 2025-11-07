<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť skupinu noriem',
        'update_heading' => 'Upraviť skupinu noriem: :title',
        'fields' => [
            'code' => 'Kód',
            'title' => 'Názov',
            'description' => 'Popis',
            'parent' => 'Patrí pod',
        ],
    ],
    'table' => [
        'heading' => 'Skupiny noriem',
        'empty_state_heading' => 'Žiadne skupiny noriem na zobrazenie',
        'row_groups' => [
        ],
        'columns' => [
            'id' => ['label' => 'ID'],
            'code' => ['label' => 'Kód'],
            'title' => ['label' => 'Názov'],
            'description' => ['label' => 'Popis'],
            'parent' => ['label' => 'Patrí pod'],
        ]
    ],
    'navigation' => [
        'label' => 'Skupiny noriem',
        'group' => 'Normy',
    ],
    'resource' => [
        'model_label' => 'Skupina noriem',
        'plural_model_label' => 'Skupiny noriem',
    ],
];