<?php

return [
    'form' => [
        'create_heading' => 'Vytvoriť normovanú činnosť',
        'update_heading' => 'Upraviť normovanú činnosť: :title',
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
    ],
    'table' => [
        'heading' => 'Normované činnosti',
        'empty_state_heading' => 'Žiadne normované činnosti na zobrazenie',
        'columns' => [
            'date' => 'Dátum',
            'ticket' => 'Zákazka',
            'template' => 'Norma',
            'state' => 'Stav',
            'subject' => 'Vozidlo',
            'note' => 'Poznámka',
            'maintenance_group' => 'Prevádzka',
            'finished_at' => 'Ukončená',
            'expected_duration' => 'Očakávané trvanie (min)',
        ]
    ],
    'navigation' => [
        'label' => 'Normované činnosti',
        'group' => 'Normy',
    ],
    'resource' => [
        'model_label' => 'Normovaná činnosť',
        'plural_model_label' => 'Normované činnosti',
    ],
];