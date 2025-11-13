<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť typ údržby',
        'update_heading' => 'Upraviť typ údržby: :title',
        'fields' => [
            'code' => [
                'label' => 'Kód',
                'helper' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
                'tooltip' => 'Jedinečný identifikátor záznamu použitý v aplikácií',
            ],
            'date' => 'Dátum',
            'title' => 'Názov',
            'description' => 'Popis',
        ],
    ],
    'table' => [
        'heading' => 'Typy údržby',
        'description' => 'Typy údržby',
        'empty_state_heading' => 'Žiadne typy údržby na zobrazenie',
        'columns' => [
            'code' => 'Kód',
            'title' => 'Názov',
            'description' => 'Popis',
        ]
    ],
    'navigation' => [
        'label' => 'Typy údržby',
        'group' => 'Kontroly',
    ],
    'resource' => [
        'model_label' => 'Typ údržby',
        'plural_model_label' => 'Typy údržby',
    ],
];